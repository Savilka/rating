<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <title>Rating</title>
</head>
<body>
<div class="container">
    <div class="relative overflow-x-auto">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    ID
                </th>
                <th scope="col" class="px-6 py-3">
                    User ID
                </th>
                <th scope="col" class="px-6 py-3">
                    Score
                </th>
                <th scope="col" class="px-6 py-3">
                    Дата создания
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach ($transactions as $transaction)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="px-6 py-4" class="">{{ $transaction->id }}</td>
                    <td class="px-6 py-4">{{ $transaction->user_id }}</td>
                    <td class="px-6 py-4">{{ $transaction->score }}</td>
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($transaction->created_at)->format('d.m.Y H:i') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="">
        {{ $transactions->links('pagination::tailwind') }}
    </div>
</div>
</body>
</html>
