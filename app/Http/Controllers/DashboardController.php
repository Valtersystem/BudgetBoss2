<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);

        $selectedDate = Carbon::create($year, $month, 1);
        $startOfSelectedMonth = $selectedDate->copy()->startOfMonth();
        $endOfSelectedMonth = $selectedDate->copy()->endOfMonth();

        // --- STATS CARDS ---

        // Transações padrão (não fixas) para o mês selecionado
        $monthlyStandardTransactions = $user->transactions()
            ->where('is_fixed', false)
            ->whereBetween('date', [$startOfSelectedMonth, $endOfSelectedMonth]);

        // Total de receitas do mês (pagas e não pagas)
        $monthlyStandardIncomesTotal = (clone $monthlyStandardTransactions)->where('type', 'income')->sum('value');
        // Total de despesas do mês (pagas e não pagas)
        $monthlyStandardExpensesTotal = (clone $monthlyStandardTransactions)->where('type', 'expense')->sum('value');

        // Transações fixas são consideradas no seu ciclo
        $fixedTransactionsActiveThisMonth = $user->transactions()
            ->where('is_fixed', true)
            ->where('date', '<=', $endOfSelectedMonth)
            ->get();

        $monthlyFixedIncomes = $fixedTransactionsActiveThisMonth->where('type', 'income')->sum('value');
        $monthlyFixedExpenses = $fixedTransactionsActiveThisMonth->where('type', 'expense')->sum('value');

        // Para os cards, mostramos o total, independente de estarem pagas ou não
        $monthlyIncomes = $monthlyStandardIncomesTotal + $monthlyFixedIncomes;
        $monthlyExpenses = $monthlyStandardExpensesTotal + $monthlyFixedExpenses;

        // --- CÁLCULO DO SALDO ATUAL (Current Balance) ---
        // Aqui, consideramos apenas as transações EFETIVADAS (is_paid = true)

        $totalInitialBalance = $user->accounts()->where('dashboard', true)->sum('initial_balance');

        // Apenas transações não fixas e PAGAS até o final do mês
        $nonFixedTransactionsUpToMonthPaid = $user->transactions()
            ->where('is_fixed', false)
            ->where('is_paid', true)
            ->where('date', '<=', $endOfSelectedMonth)
            ->get();

        $balanceFromNonFixed = $nonFixedTransactionsUpToMonthPaid->reduce(fn ($carry, $t) => $t->type === 'income' ? $carry + $t->value : $carry - $t->value, 0);

        // O saldo de transações fixas considera o valor mensal acumulado (assumimos que são pagas)
        $balanceFromFixed = $user->transactions()->where('is_fixed', true)->where('date', '<=', $endOfSelectedMonth)->get()->reduce(function ($carry, $t) use ($endOfSelectedMonth) {
            $startDate = Carbon::parse($t->date);
            if ($startDate->isAfter($endOfSelectedMonth)) {
                return $carry;
            }
            $monthsCount = ($endOfSelectedMonth->year - $startDate->year) * 12 + ($endOfSelectedMonth->month - $startDate->month) + 1;
            $totalValue = $t->value * $monthsCount;
            return $t->type === 'income' ? $carry + $totalValue : $carry - $totalValue;
        }, 0);

        $currentBalance = $totalInitialBalance + $balanceFromNonFixed + $balanceFromFixed;

        // --- DADOS DOS GRÁFICOS ---
        // Aqui também consideramos o TOTAL (pago e não pago) para ter uma visão completa do mês

        // Receitas por categoria (incluindo fixas e não pagas)
        $standardIncomesByCategory = $user->transactions()
            ->where('type', 'income')
            ->where('is_fixed', false)
            ->whereBetween('date', [$startOfSelectedMonth, $endOfSelectedMonth]) // Não filtra por 'is_paid'
            ->groupBy('category_id')
            ->select('category_id', DB::raw('SUM(value) as total'))
            ->pluck('total', 'category_id');

        $fixedIncomesByCategory = $user->transactions()
            ->where('type', 'income')
            ->where('is_fixed', true)
            ->where('date', '<=', $endOfSelectedMonth)
            ->groupBy('category_id')
            ->select('category_id', DB::raw('SUM(value) as total'))
            ->pluck('total', 'category_id');

        $allIncomesByCategory = $standardIncomesByCategory;
        foreach ($fixedIncomesByCategory as $categoryId => $total) {
            $allIncomesByCategory[$categoryId] = ($allIncomesByCategory[$categoryId] ?? 0) + $total;
        }

        $incomeCategoryIds = collect($allIncomesByCategory)->keys();
        $incomeCategories = $user->categories()->whereIn('id', $incomeCategoryIds)->get()->keyBy('id');

        $incomesByCategory = collect($allIncomesByCategory)->map(function ($total, $categoryId) use ($incomeCategories) {
            $category = $incomeCategories->get($categoryId);
            return [
                'name' => $category->name ?? 'Uncategorized',
                'color' => $category->color ?? '#8884d8',
                'total' => (float) $total,
            ];
        })->values();

        // Despesas por categoria (incluindo fixas e não pagas)
        $standardExpensesByCategory = $user->transactions()
            ->where('type', 'expense')
            ->where('is_fixed', false)
            ->whereBetween('date', [$startOfSelectedMonth, $endOfSelectedMonth]) // Não filtra por 'is_paid'
            ->groupBy('category_id')
            ->select('category_id', DB::raw('SUM(value) as total'))
            ->pluck('total', 'category_id');

        $fixedExpensesByCategory = $user->transactions()
            ->where('type', 'expense')
            ->where('is_fixed', true)
            ->where('date', '<=', $endOfSelectedMonth)
            ->groupBy('category_id')
            ->select('category_id', DB::raw('SUM(value) as total'))
            ->pluck('total', 'category_id');

        $allExpensesByCategory = $standardExpensesByCategory;
        foreach ($fixedExpensesByCategory as $categoryId => $total) {
            $allExpensesByCategory[$categoryId] = ($allExpensesByCategory[$categoryId] ?? 0) + $total;
        }

        $expenseCategoryIds = collect($allExpensesByCategory)->keys();
        $expenseCategories = $user->categories()->whereIn('id', $expenseCategoryIds)->get()->keyBy('id');

        $expensesByCategory = collect($allExpensesByCategory)->map(function ($total, $categoryId) use ($expenseCategories) {
            $category = $expenseCategories->get($categoryId);
            return [
                'name' => $category->name ?? 'Uncategorized',
                'color' => $category->color ?? '#EF4444',
                'total' => (float) $total,
            ];
        })->values();

        // Frequência de despesas (total, não apenas pagas)
        $expenseFrequency = collect(range(6, 0))->map(function ($i) use ($user) {
            $date = Carbon::now()->subDays($i);
            return [
                'date' => $date->format('M d'),
                'total' => (float) $user->transactions()
                                        ->where('type', 'expense')
                                        ->whereDate('date', $date->toDateString())
                                        ->sum('value'),
            ];
        });

        // Incomes vs Expenses (total, não apenas pagas)
        $incomesVsExpenses = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();

            // Receitas padrão do mês (total)
            $standardIncomes = $user->transactions()
                ->where('type', 'income')
                ->where('is_fixed', false)
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->sum('value');

            // Receitas fixas ativas no mês
            $fixedIncomes = $user->transactions()
                ->where('type', 'income')
                ->where('is_fixed', true)
                ->where('date', '<=', $monthEnd)
                ->sum('value');

            $incomes = $standardIncomes + $fixedIncomes;

            // Despesas padrão do mês (total)
            $standardExpenses = $user->transactions()
                ->where('type', 'expense')
                ->where('is_fixed', false)
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->sum('value');

            // Despesas fixas ativas no mês
            $fixedExpenses = $user->transactions()
                ->where('type', 'expense')
                ->where('is_fixed', true)
                ->where('date', '<=', $monthEnd)
                ->sum('value');

            $expenses = $standardExpenses + $fixedExpenses;

            $incomesVsExpenses[] = ['name' => $date->format('n/Y'), 'incomes' => (float) $incomes, 'expenses' => (float) $expenses];
        }

        // Pendências (apenas não fixas e não pagas)
        $outstandingExpenses = (clone $monthlyStandardTransactions)->where('type', 'expense')->where('is_paid', false)->sum('value');
        $outstandingIncomes = (clone $monthlyStandardTransactions)->where('type', 'income')->where('is_paid', false)->sum('value');

        return Inertia::render('Dashboard', [
            'stats' => [
                'current_balance' => $currentBalance,
                'monthly_incomes' => $monthlyIncomes,
                'monthly_expenses' => $monthlyExpenses,
                'outstanding_incomes_total' => $outstandingIncomes,
                'outstanding_expenses_total' => $outstandingExpenses,
            ],
            'filters' => ['year' => (int)$year, 'month' => (int)$month],
            'incomesByCategory' => $incomesByCategory,
            'expensesByCategory' => $expensesByCategory,
            'expenseFrequency' => $expenseFrequency,
            'incomesVsExpenses' => $incomesVsExpenses,
        ]);
    }
}

