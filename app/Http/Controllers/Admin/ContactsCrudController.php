<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;

use App\Http\Requests\ContactsRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ContactsCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ContactsCrudController extends CrudController
{


    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'email' => 'required|email',
                'phone' => 'required|string',
                'mobile' => 'required|string',
                'type' => 'required|numeric',
                'location_id' => 'required|exists:locations,id', // Validate that location_id exists in the locations table
            ]);

            // Create a new contact record with the validated data
            $contact = \App\Models\Contacts::create($validatedData);

            // Optionally, you can redirect to a success page or return a response
            return redirect()->route('contacts.index')->with('success', 'Contact created successfully!');
        } catch (\Exception $e) {
            // Log or handle the exception as needed
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Contacts::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/contacts');
        CRUD::setEntityNameStrings('contacts', 'contacts');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::setFromDb(); // set columns from db columns.

        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        // CRUD::setValidation(ContactsRequest::class);
        CRUD::setValidation();
        CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */

         $locations = \App\Models\Locations::pluck('country', 'id')->toArray();

        //  dd($locations);

        CRUD::addField([
            'label' => 'Location',
            'type' => 'select',
            'name' => 'location_id', // Make sure this matches the column name in your contacts table
            'entity' => 'location',
            'attribute' => 'country',
            // 'options' => $locations, // Populate options with fetched locations
            'model' => 'App\Models\Locations', // Update with the correct namespace for your Location model
            'attributes' => [ // Additional attributes for the select field
                'placeholder' => 'Select Location', // Placeholder text for the select field
            ],
        ]);
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }




    // Assuming your form sends the location_id field along with other contact details


}