<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Reports - StrideSync Admin</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-black text-white min-h-screen relative overflow-y-auto">

<div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-30" style="background-image: url('{{ asset('images/user-bg.jpg') }}');"></div>

<div class="max-w-6xl mx-auto relative z-10 p-8">
    <div class="mb-6">
        <a href="{{ route('admin.telegram.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white text-black font-semibold shadow hover:bg-gray-200 transition">
            Back to Telegram Bot Management
        </a>
    </div>

    <div class="mb-6">
        <h1 class="text-4xl font-bold mb-2 text-emerald-300">User Reports</h1>
        <p class="text-slate-200">Reports submitted from the Telegram bot</p>
    </div>

    <div class="bg-white/95 text-slate-900 rounded-3xl p-6 border-4 border-black shadow-[6px_6px_0_rgba(0,0,0,1)]">
        @if($reports->isEmpty())
            <p class="text-slate-600">No reports yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-100">
                        <tr class="text-left">
                            <th class="px-4 py-3">Reported User</th>
                            <th class="px-4 py-3">Reporter</th>
                            <th class="px-4 py-3">Reason</th>
                            <th class="px-4 py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                            <tr class="border-b last:border-b-0">
                                <td class="px-4 py-3">
                                    {{ $report->target->name ?? 'Unknown' }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $report->reporter->name ?? 'Unknown' }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $report->reason }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $report->created_at->format('Y-m-d H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

</body>
</html>


