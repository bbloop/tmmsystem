<x-layouts.app-purple title="Content Calendar">

    <div class="max-w-6xl mx-auto py-8">

        <!-- HEADER -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-purple-700">
                {{ $project->name }}
            </h1>
            <p class="text-gray-500 text-sm mt-1">
                Content Calendar Management
            </p>
        </div>

        @php
        $statusColors = [
        'draft' => 'bg-gray-100 text-gray-600',
        'submitted' => 'bg-yellow-100 text-yellow-700',
        'approved' => 'bg-green-100 text-green-700',
        'rejected' => 'bg-red-100 text-red-600',
        ];

        $status = $project->calendar_status ?? 'draft';
        $calendarEditable = in_array($status, ['draft','rejected']) || is_null($status);
        @endphp

        <!-- STATUS CARD -->
        <div class="bg-white rounded-3xl shadow-sm border border-purple-100 p-6 mb-10 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-purple-700">
                    Calendar Overview
                </h3>
            </div>

            <span class="px-4 py-1 rounded-full text-xs font-semibold {{ $statusColors[$status] ?? 'bg-gray-100 text-gray-600' }}">
                {{ ucfirst($status) }}
            </span>
        </div>

        <!-- CREATE CONTENT -->
        <!-- CREATE CONTENT -->
        <form method="POST" action="{{ route('smm.tasks.store', $project) }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <input name="title" placeholder="Post Title" required class="border border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-purple-300" @disabled(!$calendarEditable)>

                <select name="platform" required class="border border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-purple-300" @disabled(!$calendarEditable)>
                    <option value="">Select Platform</option>
                    <option>Facebook</option>
                    <option>Instagram</option>
                    <option>TikTok</option>
                </select>

            </div>

            <!-- Scheduled Posting Date -->
            <div>
                <label class="block text-sm font-medium text-purple-600 mb-1">
                    Scheduled Posting Date
                </label>

                <input type="datetime-local" name="scheduled_at" required class="w-full border border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-purple-300" @disabled(!$calendarEditable)>
            </div>

            <!-- Creative Production Deadline -->
            <div>
                <label class="block text-sm font-medium text-purple-600 mb-1">
                    Creative Production Deadline
                </label>

                <input type="datetime-local" name="due_at" required class="w-full border border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-purple-300" @disabled(!$calendarEditable)>
            </div>

            <!-- Caption -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-purple-600 mb-1">
                    Caption / Content Details
                </label>

                <textarea name="description" placeholder="Write the caption or content description..." class="w-full border border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-purple-300" @disabled(!$calendarEditable)></textarea>
            </div>

            <!-- Inspiration -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-purple-600 mb-1">
                    Inspiration / Peg Link (Optional)
                </label>

                <input type="url" name="inspo_link" placeholder="Paste reference link here" class="w-full border border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-purple-300" @disabled(!$calendarEditable)>
            </div>

            <div class="mt-6">
                <button type="submit" class="px-6 py-3 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition disabled:opacity-50" @disabled(!$calendarEditable)>
                    Add to Calendar
                </button>
            </div>

        </form>
        <!-- SCHEDULED CONTENT -->
        <div class="bg-white rounded-3xl shadow-sm border border-purple-100 p-8">

            <h3 class="text-xl font-semibold text-purple-700 mb-6">
                Scheduled Content
            </h3>

            @if($tasks->count() === 0)

            <div class="text-center py-12 text-gray-400">
                No content items yet.
            </div>

            @else

            <div class="overflow-x-auto">
                <table class="w-full text-sm">

                    <thead class="bg-purple-50">
                        <tr>
                            <th class="p-4 text-left">Title</th>
                            <th class="p-4 text-left">Platform</th>
                            <th class="p-4 text-left">Scheduled</th>
                            <th class="p-4 text-left">Deadline</th>
                            <th class="p-4 text-left">Status</th>
                            <th class="p-4 text-left">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">

                        @foreach($tasks as $task)
                        <tr class="hover:bg-purple-50 transition">

                            <td class="p-4 font-medium">
                                {{ $task->title }}
                            </td>

                            <td class="p-4">
                                {{ $task->platform }}
                            </td>

                            <td class="p-4">
                                {{ optional($task->scheduled_at)->format('M d, Y H:i') }}
                            </td>

                            <td class="p-4">
                                {{ optional($task->due_at)->format('M d, Y H:i') }}
                            </td>

                            <td class="p-4">
                                <span class="px-3 py-1 text-xs rounded-full bg-purple-100 text-purple-700">
                                    {{ $task->getStatusLabel() }}
                                </span>
                            </td>

                            <td class="p-4">

                                {{-- 1️⃣ AWAITING SMM REVIEW --}}
                                @if($task->status === \App\Models\Task::STATUS_AWAITING_SMM)

                                <div class="flex flex-col gap-2">

                                    <form method="POST" action="{{ route('smm.tasks.review', $task) }}">
                                        @csrf
                                        <input type="hidden" name="action" value="approve">
                                        <button class="bg-green-600 text-white px-3 py-2 rounded-xl text-sm hover:bg-green-700">
                                            Approve → Send to CEO
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('smm.tasks.review', $task) }}">
                                        @csrf
                                        <input type="hidden" name="action" value="reject">

                                        <input type="text" name="rejection_reason" placeholder="Reason for rejection..." required class="border rounded-xl p-2 text-sm">

                                        <button class="bg-red-600 text-white px-3 py-2 rounded-xl text-sm hover:bg-red-700">
                                            Reject → Return to Creative
                                        </button>
                                    </form>

                                    <div class="mt-8">
                                        <button class="px-6 py-3 bg-purple-100 text-purple-700 rounded-xl hover:bg-purple-200 transition disabled:opacity-50" @disabled(!$calendarEditable)>
                                            Add to Calendar
                                        </button>
                                    </div>


                                </div>

                                {{-- 2️⃣ ALREADY SENT TO CEO --}}
                                @elseif($task->status === \App\Models\Task::STATUS_SUBMITTED)

                                <span class="text-yellow-600 text-sm">
                                    Forwarded to CEO
                                </span>

                                {{-- 3️⃣ CEO APPROVED → READY TO POST --}}
                                @elseif($task->status === \App\Models\Task::STATUS_APPROVED)

                                <form method="POST" action="{{ route('smm.tasks.posted', $task) }}">
                                    @csrf
                                    <button class="bg-purple-600 text-white px-3 py-2 rounded-xl text-sm hover:bg-purple-700">
                                        Mark as Posted
                                    </button>
                                </form>

                                {{-- 3️⃣ AFTER CEO APPROVES CALENDAR --}}
                                @elseif($status === 'approved' && $task->status === \App\Models\Task::STATUS_DRAFT)

                                <form method="POST" action="{{ route('smm.tasks.assign', $task) }}" class="flex flex-col gap-2">
                                    @csrf

                                    <select name="assigned_to" class="border rounded-xl p-2 text-sm" required>
                                        <option value="">-- Select Creative --</option>
                                        @foreach($creatives as $creative)
                                        <option value="{{ $creative->id }}">
                                            {{ $creative->name }}
                                        </option>
                                        @endforeach
                                    </select>

                                    <input type="datetime-local" name="due_at" class="border rounded-xl p-2 text-sm" required>

                                    <button class="bg-purple-600 text-white px-3 py-2 rounded-xl text-sm hover:bg-purple-700">
                                        Assign
                                    </button>
                                </form>

                                {{-- 4️⃣ EDIT MODE --}}
                                @elseif($calendarEditable)

                                <a href="{{ route('smm.tasks.edit', $task) }}" class="text-blue-600 hover:underline">
                                    Edit
                                </a>

                                <form method="POST" action="{{ route('smm.tasks.destroy', $task) }}" class="inline ml-2">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:underline">
                                        Delete
                                    </button>
                                </form>

                                {{-- 5️⃣ LOCKED --}}
                                @else

                                <span class="text-gray-400 text-xs">
                                    Editing locked
                                </span>

                                @endif

                            </td>

                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

            {{-- SUBMIT CALENDAR --}}
            @php
            $canSubmit = $tasks->count() > 0 && in_array($status, ['draft','rejected']);
            @endphp

            @if($canSubmit)
            <div class="mt-8 border-t pt-6 flex flex-col items-end">
                <form method="POST" action="{{ route('smm.calendar.submit', $project) }}">
                    @csrf
                    <button class="px-6 py-3 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition shadow-sm">
                        Submit Calendar to CEO
                    </button>
                </form>
            </div>
            @endif

            @endif

        </div>

    </div>

</x-layouts.app-purple>
