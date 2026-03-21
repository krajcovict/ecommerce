@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'block mt-1 w-full border-gray-300 focus:border-purple-500 focus:outline-none focus:ring-purple-500 rounded-md']) }}>
