<?php
function calculateBillAmount($units, $service_type) {
    $rates = [
        ['limit' => 50, 'rate' => 1.5],
        ['limit' => 100, 'rate' => 2.5],
        ['limit' => 150, 'rate' => 3.5],
        ['limit' => PHP_INT_MAX, 'rate' => 4.5]
    ];
    
    if ($service_type == 'Commercial') {
        foreach ($rates as &$rate) {
            $rate['rate'] += 1;
        }
    } elseif ($service_type == 'Industrial') {
        foreach ($rates as &$rate) {
            $rate['rate'] += 2;
        }
    }
    
    if ($units == 0) {
        return 25;
    }
    
    $amount = 0;
    $remaining_units = $units;
    $previous_limit = 0;
    
    foreach ($rates as $slab) {
        $slab_units = min($slab['limit'] - $previous_limit, $remaining_units);
        if ($slab_units > 0) {
            $amount += $slab_units * $slab['rate'];
            $remaining_units -= $slab_units;
        }
        $previous_limit = $slab['limit'];
        
        if ($remaining_units <= 0) {
            break;
        }
    }
    
    return round($amount, 2);
}

function calculateDueDate($bill_date) {
    $due_date = date('Y-m-d', strtotime($bill_date . ' +15 days'));
    return $due_date;
}

function calculateFine($due_date) {
    $today = date('Y-m-d');
    if ($today > $due_date) {
        return 150;
    }
    return 0;
}
?>
