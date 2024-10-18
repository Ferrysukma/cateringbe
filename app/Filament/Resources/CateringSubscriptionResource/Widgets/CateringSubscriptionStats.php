<?php

namespace App\Filament\Resources\CateringSubscriptionResource\Widgets;

use App\Models\CateringSubscription;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CateringSubscriptionStats extends BaseWidget
{
    protected function getStats(): array {
        $totalTransactions    = CateringSubscription::count();
        $approvedTransactions = CateringSubscription::where('is_paid', 1)->count();
        $totalRevenue         = CateringSubscription::where('is_paid', 1)->sum('total_amount');

        return [
            Stat::make('Total Transactions', $totalTransactions)
            ->description('All Transactions')
            ->descriptionIcon('heroicon-o-currency-dollar'),

            Stat::make('Approved Transactions', $approvedTransactions)
            ->description('Approved Transactions')
            ->descriptionIcon('heroicon-o-check-circle')
            ->color('success'),
            
            Stat::make('Total Revenue', 'IDR ' . $totalRevenue)
            ->description('Revenue from appoved transactions')
            ->descriptionIcon('heroicon-o-check-circle')
            ->color('success'),
        ];
    }
}
