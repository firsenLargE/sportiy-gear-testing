<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\District;
use App\Models\ShippingZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'phone_no'      => 'required|string|max:20',
            'email'         => 'nullable|email|max:255',
            'province_id'   => 'required|exists:provinces,id',
            'district_id'   => 'required|exists:districts,id',
            'address_line1' => 'required|string|max:500',
            'address_line2' => 'nullable|string|max:500',
            'nearest_landmark' => 'nullable|string|max:255',
        ]);

        $shippingZone = ShippingZone::where('province_id', $request->province_id)
            ->where('district_id', $request->district_id)
            ->where('is_active', true)
            ->first();

        if (!$shippingZone) {
            return response()->json([
                'success' => false,
                'message' => 'Shipping is not available for the selected province and district. Please choose another location.',
            ], 422);
        }

        $address = Address::create([
            'user_id'          => Auth::id(),
            'name'             => $request->name,
            'phone_no'         => $request->phone_no,
            'email'            => $request->email,
            'province_id'      => $request->province_id,
            'district_id'      => $request->district_id,
            'address_line1'    => $request->address_line1,
            'address_line2'    => $request->address_line2,
            'nearest_landmark' => $request->nearest_landmark,
            'shipping_zone_id' => $shippingZone->id,
        ]);

        $address->load(['province', 'district', 'shippingZone']);

        return response()->json([
            'success'      => true,
            'message'      => 'Address added successfully',
            'address'      => $address,
            'shipping_fee' => $shippingZone->shipping_fee,
        ]);
    }

    public function update(Request $request, $id)
    {
        $address = Address::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'name'          => 'required|string|max:255',
            'phone_no'      => 'required|string|max:20',
            'email'         => 'nullable|email|max:255',
            'province_id'   => 'required|exists:provinces,id',
            'district_id'   => 'required|exists:districts,id',
            'address_line1' => 'required|string|max:500',
            'address_line2' => 'nullable|string|max:500',
            'nearest_landmark' => 'nullable|string|max:255',
        ]);

        $shippingZone = ShippingZone::where('province_id', $request->province_id)
            ->where('district_id', $request->district_id)
            ->where('is_active', true)
            ->first();

        if (!$shippingZone) {
            return response()->json([
                'success' => false,
                'message' => 'Shipping is not available for the selected province and district.',
            ], 422);
        }

        $address->update([
            'name'             => $request->name,
            'phone_no'         => $request->phone_no,
            'email'            => $request->email,
            'province_id'      => $request->province_id,
            'district_id'      => $request->district_id,
            'address_line1'    => $request->address_line1,
            'address_line2'    => $request->address_line2,
            'nearest_landmark' => $request->nearest_landmark,
            'shipping_zone_id' => $shippingZone->id,
        ]);

        $address->load(['province', 'district', 'shippingZone']);

        return response()->json([
            'success'      => true,
            'message'      => 'Address updated successfully',
            'address'      => $address,
            'shipping_fee' => $shippingZone->shipping_fee,
        ]);
    }

    public function destroy($id)
    {
        $address = Address::where('user_id', Auth::id())->findOrFail($id);
        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully',
        ]);
    }

    public function getDistricts($provinceId)
    {
        $districts = District::where('province_id', $provinceId)
            ->whereHas('shippingZones', function ($query) {
                $query->where('is_active', true);
            })
            ->get(['id', 'name']);

        return response()->json($districts);
    }

    public function getShippingFee($addressId)
    {
        $address = Address::with('shippingZone')->findOrFail($addressId);
        $shippingFee = $address->shippingZone ? $address->shippingZone->shipping_fee : 0;
        return response()->json(['shipping_fee' => $shippingFee]);
    }

    public function userAddresses()
    {
        $addresses = Address::with(['province', 'district'])
            ->where('user_id', Auth::id())
            ->get();
        return response()->json(['addresses' => $addresses]);
    }


    public function editData($id)
    {
        $address = Address::with(['province', 'district'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);
        return response()->json(['address' => $address]);
    }
}
