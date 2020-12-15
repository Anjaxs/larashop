<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User\Address;
use App\Services\User\Address\CreateAddress;

class AddressesController extends Controller
{
    public function index(Request $request)
    {
        return view('addresses.index', [
            'addresses' => $request->user()->addresses,
        ]);
    }

    public function create()
    {
        return view('addresses.create_and_edit', [
            'address' => new Address()
        ]);
    }

    public function store(Request $request)
    {
        $data = ['user_id' => auth()->id()] + $request->all();

        app(CreateAddress::class)->execute($data);

        return redirect()->route('addresses.index');
    }
}
