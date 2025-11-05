<template>
    <GuestLayout title="Set new password">
      <form class="space-y-6" action="#" method="POST">

        <div>
          <div class="mt-2">
            <input type="new-password" name="password" id="password" placeholder="new password" required="" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
          </div>
          <div class="mt-2">
            <label for="password-repeat" class="block text-sm/6 font-medium text-gray-900">Repeat password</label>
            <input type="password" name="password_repeat" id="password-repeat" placeholder="repeat password" required="" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
          </div>
        </div>

        <div>
          <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Submit</button>
        </div>
      </form>
    </GuestLayout>
</template>

<script setup>
import GuestLayout from "../components/GuestLayout.vue";
</script>

<style scoped>

</style>
