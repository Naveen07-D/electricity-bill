#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include "consumer.h"
#include "input.h"
#include "validation.h"
#include "billing.h"
#include "output.h"
#include "utils.h"

#define MAX_CONSUMERS 100

int main() {
    clear_screen();
    print_header("ELECTRICITY BILL MANAGEMENT SYSTEM");
    
    char service_numbers[MAX_CONSUMERS][MAX_SERVICE_LEN];
    int service_count = 0;
    
    get_service_numbers(service_numbers, &service_count);
    
    Consumer consumer;
    init_consumer(&consumer);
    
    get_consumer_details(&consumer);
    
    if (!validate_consumer_data(&consumer, service_numbers, service_count)) {
        printf("\nRegistration failed! Please correct the errors and try again.\n");
        return 1;
    }
    
    add_service_number(consumer.service_number, service_numbers, &service_count);
    
    process_billing(&consumer);
    
    display_bill(&consumer);
    display_summary(&consumer);
    
    char choice;
    printf("\nDo you want to save this bill? (y/n): ");
    scanf(" %c", &choice);
    getchar();
    
    if (choice == 'y' || choice == 'Y') {
        save_bill_to_file(&consumer);
    }
    
    printf("\nThank you for using Electricity Bill Management System!\n");
    printf("Press Enter to exit...");
    getchar();
    
    return 0;
}

