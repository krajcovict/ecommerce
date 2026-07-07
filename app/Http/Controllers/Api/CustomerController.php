<?php

namespace App\Http\Controllers\Api;

use App\Enums\AddressType;
use App\Enums\CustomerStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CountryResource;
use App\Http\Resources\CustomerListResource;
use App\Http\Resources\CustomerResource;
use App\Models\Country;
use App\Models\Customer;
use App\Models\CustomerAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request('search', false);
        $perPage = request('per_page', 20);
        $sortField = request('sort_field', 'created_at');
        $sortDirection = strtolower(request('sort_direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        $sortableFields = [
            'id' => 'customers.user_id',
            'name' => 'customers.first_name',
            'email' => 'users.email',
            'phone' => 'customers.phone',
            'status' => 'customers.status',
            'created_at' => 'customers.created_at',
            'updated_at' => 'customers.updated_at',
        ];

        $sortColumn = $sortableFields[$sortField] ?? 'customers.created_at';

        $query = Customer::query()
            ->with('user')
            ->orderBy($sortColumn, $sortDirection);

        if ($search) {
            $query->where(DB::raw("CONCAT(customers.first_name, ' ', customers.last_name)"), 'like', "%{$search}%")
                ->orWhere('customers.user_id', 'like', "%{$search}%")
                ->orWhere('users.email', 'like', "%{$search}%")
                ->orWhere('customers.phone', 'like', "%{$search}%")
                ->orWhere('customers.status', 'like', "%{$search}%")
                ->orWhere('customers.created_at', 'like', "%{$search}%");
        }

        return CustomerListResource::collection(
            $query->paginate($perPage)
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        return new CustomerResource($customer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CustomerRequest $request, Customer $customer)
    {
        $customerData = $request->validated();
        $customerData['updated_by'] = $request->user()->id;
        $customerData['status'] = $customerData['status'] ? CustomerStatus::Active->value : CustomerStatus::Disabled->value;
        $shippingData = $customerData['shippingAddress'];
        $billingData = $customerData['billingAddress'];

        DB::beginTransaction();
        try {
            $customer->update($customerData);

            if ($customer->shippingAddress) {
                $customer->shippingAddress->update($shippingData);
            } else {
                $shippingData['customer_id'] = $customer->user_id;
                $shippingData['type'] = AddressType::Shipping->value;
                CustomerAddress::create($shippingData);
            }

            if ($customer->billingAddress) {
                $customer->billingAddress->update($billingData);
            } else {
                $billingData['customer_id'] = $customer->user_id;
                $billingData['type'] = AddressType::Billing->value;
                CustomerAddress::create($billingData);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::critical(__METHOD__ . " method failed. " . $e->getMessage());
            throw $th;
        }
        DB::commit();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->noContent();
    }

    public function countries() {
        return CountryResource::collection(Country::query()->orderBy('name', 'asc')->get());
    }
}
