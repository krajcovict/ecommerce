<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Order Details') }}
        </h2>
    </x-slot>

    <main class="p-5">
      <div class="container lg:w-2/3 xl:w-2/3 mx-auto">
        <h1 class="text-3xl font-bold mb-6">Order #{{ $order->id }} Details</h1>

        <div class="bg-white p-3 rounded-md shadow-md">
          <div>
            <table class="table-sm">
              <tbody>
                <tr>
                  <td class="font-bold">Order #</td>
                  <td>{{ $order->id }}</td>
                </tr>
                <tr>
                  <td class="font-bold">Order Date</td>
                  <td>{{ $order->created_at }}</td>
                </tr>
                <tr>
                  <td class="font-bold">Status</td>
                  <td>
                    <span class="bg-emerald-500 text-white p-1 rounded {{ $order->isPaid() ? 'bg-emerald-500' : 'bg-gray-400' }}">
                        {{ $order->status }}
                    </span>
                  </td>
                </tr>
                <tr>
                  <td class="font-bold">SubTotal</td>
                  <td>${{ $order->total_price }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <hr class="my-5" />

          <!-- Order Items -->
          <div>
            <!-- Order Item -->
              @foreach ($order->items as $item)
                <div class="flex gap-6 pb-2">
                  <a href="{{ route('product.view', $item->product) }}"
                    class="w-16 h-16 flex items-center justify-center overflow-hidden border border-gray-300">
                    <img src="{{ $item->product->image }}" class="object-cover" alt="" />
                  </a>
                  <div class="flex-1 flex flex-col justify-between pb-3">
                    <h3 class="text-ellipsis mb-4">
                      {{ $item->product->title }}
                    </h3>
                  </div>
                  <div class="flex flex-col justify-between items-end pb-3 w-32">
                    <div class="text-lg mb-4">${{ $item->unit_price }}</div>
                  </div>
                  <div class="flex flex-col justify-between items-end pb-3 w-32">
                    <div class="text-lg mb-4">Quantity: {{ $item->quantity }}</div>
                  </div>
                </div>
              @endforeach
            <!--/ Order Item -->
            <hr class="my-2" />

          </div>
          <!--/ Order Items -->

          <div class="border-t border-gray-300 pt-4">
            @if (!$order->isPaid())
                <form action="{{ route('checkout-order', $order) }}" method="post">
                    @csrf
                  <button class="btn-primary flex justify-center items-center w-full py-3 text-lg">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-6 w-6 mr-1"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                      stroke-width="2"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"
                      />
                    </svg>
                    Proceed to Pay
                  </button>
                </form>
            @endif
          </div>
          </div>
        </div>
      </div>
    </main>
</x-app-layout>
