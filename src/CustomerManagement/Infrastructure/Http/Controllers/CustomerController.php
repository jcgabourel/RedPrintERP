<?php

namespace Src\CustomerManagement\Infrastructure\Http\Controllers;

use App\Http\Controllers\Controller;
use Src\CustomerManagement\Application\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    private CustomerService $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function index(): JsonResponse
    {
        try {
            $customers = $this->customerService->getAllCustomers();
            
            return response()->json([
                'success' => true,
                'data' => array_map(fn($customer) => $customer->toArray(), $customers),
                'total' => count($customers)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving customers: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'rfc' => 'required|string|max:13',
                'address' => 'required|string',
                'phone' => 'required|string',
                'email' => 'required|email'
            ]);

            $customer = $this->customerService->createCustomer(
                $validated['name'],
                $validated['rfc'],
                $validated['address'],
                $validated['phone'],
                $validated['email']
            );

            return response()->json([
                'success' => true,
                'message' => 'Customer created successfully',
                'data' => $customer->toArray()
            ], 201);

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating customer: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $customer = $this->customerService->getCustomerById($id);

            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $customer->toArray()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving customer: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'rfc' => 'sometimes|required|string|max:13',
                'address' => 'sometimes|required|string',
                'phone' => 'sometimes|required|string',
                'email' => 'sometimes|required|email'
            ]);

            $customer = $this->customerService->updateCustomer(
                $id,
                $validated['name'],
                $validated['rfc'],
                $validated['address'],
                $validated['phone'],
                $validated['email']
            );

            return response()->json([
                'success' => true,
                'message' => 'Customer updated successfully',
                'data' => $customer->toArray()
            ]);

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating customer: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $result = $this->customerService->deleteCustomer($id);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found or could not be deleted'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Customer deleted successfully'
            ]);

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting customer: ' . $e->getMessage()
            ], 500);
        }
    }

    public function search(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|min:2'
            ]);

            $customers = $this->customerService->searchCustomersByName($request->name);

            return response()->json([
                'success' => true,
                'data' => array_map(fn($customer) => $customer->toArray(), $customers),
                'total' => count($customers)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error searching customers: ' . $e->getMessage()
            ], 500);
        }
    }
}