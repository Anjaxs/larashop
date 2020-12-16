<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User\Address;
use App\Services\User\Address\CreateAddress;
use App\Services\User\Address\DestroyAddress;
use App\Services\User\Address\UpdateAddress;

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

    public function edit(Address $address)
    {
        return view('addresses.create_and_edit', [
            'address' => $address
        ]);
    }

    public function update($id, Request $request)
    {
        $data = [
            'user_id' => auth()->id(),
            'address_id' => $id
        ] + $request->all();

        app(UpdateAddress::class)->execute($data);

        return redirect()->route('addresses.index');
    }

    public function destroy($id)
    {
        $data = [
            'user_id' => auth()->id(),
            'address_id' => $id
        ];

        app(DestroyAddress::class)->execute($data);

        return [];
    }
}
