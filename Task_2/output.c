#include <stdio.h>
#include <string.h>
#include <time.h>
#include "output.h"
#include "utils.h"

void display_bill(const Consumer *consumer) {
    clear_screen();
    print_header("ELECTRICITY BILL");
    
    printf("\n╔══════════════════════════════════════════════════════════╗\n");
    printf("║ CONSUMER DETAILS                                          ║\n");
    printf("╠══════════════════════════════════════════════════════════╣\n");
    printf("║ Name:           %-40s ║\n", consumer->name);
    printf("║ Phone:          %-40s ║\n", consumer->phone);
    printf("║ Service No:     %-40s ║\n", consumer->service_number);
    printf("║ Category:       %-40s ║\n", consumer->category);
    printf("╚══════════════════════════════════════════════════════════╝\n");
    
    printf("\n╔══════════════════════════════════════════════════════════╗\n");
    printf("║ METER READINGS                                            ║\n");
    printf("╠══════════════════════════════════════════════════════════╣\n");
    printf("║ Previous Reading: %-36.2f ║\n", consumer->prev_reading);
    printf("║ Current Reading:  %-36.2f ║\n", consumer->curr_reading);
    printf("║ Units Consumed:   %-36.2f ║\n", consumer->units);
    printf("╚══════════════════════════════════════════════════════════╝\n");
    
    printf("\n╔══════════════════════════════════════════════════════════╗\n");
    printf("║ BILL AMOUNT CALCULATION                                   ║\n");
    printf("╠══════════════════════════════════════════════════════════╣\n");
    printf("║ Bill Amount:             ₹ %-33.2f ║\n", consumer->amount);
    printf("║ Fine (After Due Date):   ₹ %-33.2f ║\n", consumer->fine);
    printf("╠══════════════════════════════════════════════════════════╣\n");
    printf("║ TOTAL AMOUNT:            ₹ %-33.2f ║\n", consumer->total_amount);
    printf("╚══════════════════════════════════════════════════════════╝\n");
    
    printf("\n╔══════════════════════════════════════════════════════════╗\n");
    printf("║ PAYMENT DETAILS                                           ║\n");
    printf("╠══════════════════════════════════════════════════════════╣\n");
    printf("║ Bill Date:      %-40s ║\n", consumer->bill_date);
    printf("║ Due Date:       %-40s ║\n", consumer->due_date);
    printf("║ Amount After Due Date: ₹ %-33.2f ║\n", consumer->total_amount);
    printf("╚══════════════════════════════════════════════════════════╝\n");
    
    printf("\nNote: Due date is 15 days from bill date.\n");
    printf("      Fine of ₹150 is applicable after due date.\n");
}

void display_summary(const Consumer *consumer) {
    printf("\n┌──────────────────────────────────────────────────────────┐\n");
    printf("│ BILL SUMMARY                                              │\n");
    printf("├──────────────────────────────────────────────────────────┤\n");
    printf("│ Service No: %-45s │\n", consumer->service_number);
    printf("│ Name:       %-45s │\n", consumer->name);
    printf("│ Units:      %-45.2f │\n", consumer->units);
    printf("│ Amount:     ₹%-44.2f │\n", consumer->amount);
    printf("│ Due Date:   %-45s │\n", consumer->due_date);
    printf("│ Total Due:  ₹%-44.2f │\n", consumer->total_amount);
    printf("└──────────────────────────────────────────────────────────┘\n");
}

void save_bill_to_file(const Consumer *consumer) {
    FILE *file = fopen("bills_history.txt", "a");
    if (file == NULL) {
        printf("Error: Could not save bill to file.\n");
        return;
    }
    
    fprintf(file, "\n══════════════════════════════════════════════════════════\n");
    fprintf(file, "ELECTRICITY BILL - %s\n", consumer->bill_date);
    fprintf(file, "══════════════════════════════════════════════════════════\n");
    fprintf(file, "Consumer: %s\n", consumer->name);
    fprintf(file, "Service No: %s\n", consumer->service_number);
    fprintf(file, "Phone: %s\n", consumer->phone);
    fprintf(file, "Category: %s\n", consumer->category);
    fprintf(file, "Previous Reading: %.2f\n", consumer->prev_reading);
    fprintf(file, "Current Reading: %.2f\n", consumer->curr_reading);
    fprintf(file, "Units Consumed: %.2f\n", consumer->units);
    fprintf(file, "Bill Amount: ₹%.2f\n", consumer->amount);
    fprintf(file, "Fine: ₹%.2f\n", consumer->fine);
    fprintf(file, "Total Amount: ₹%.2f\n", consumer->total_amount);
    fprintf(file, "Bill Date: %s\n", consumer->bill_date);
    fprintf(file, "Due Date: %s\n", consumer->due_date);
    fprintf(file, "══════════════════════════════════════════════════════════\n");
    
    fclose(file);
    printf("\nBill saved to 'bills_history.txt'\n");
}

