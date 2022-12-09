<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactsController extends Controller
{
    public function index()
    {
        $contacts = Contact::all();

        return view('contacts_list', compact('contacts'));
    }

    public function create()
    {
        return view('create');
    }

    public function store(Request $request)
    {
        $contact = $request->except([
           '_token',
        ]);

        Contact::query()->create($contact);

        return redirect()->route('index');
    }

    public function edit($id)
    {
        $contact = Contact::query()->find($id);

        return view('edit', compact('contact'));
    }

    public function update(Request $request, $id)
    {
        $contact = $request->except([
            '_token',
        ]);

        Contact::query()->find($id)->update($contact);

        return redirect()->route('index');
    }

    public function destroy(Request $request, $id)
    {
        Contact::query()->find($id)->delete();

        return redirect()->route('index');
    }
}
