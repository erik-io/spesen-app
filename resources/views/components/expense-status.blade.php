@use('App\Models\Expense')

<span class="px-2 inline-flex text-sm font-medium leading-5 rounded-full
    @if($status == Expense::STATUS_PENDING) bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200
    @elseif($status == Expense::STATUS_APPROVED) bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200
    @elseif($status == Expense::STATUS_REJECTED) bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200
    @endif">
    {{ __(ucfirst($status)) }}
</span>

