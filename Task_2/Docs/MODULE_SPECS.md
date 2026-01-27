# **Module Specifications**

## **Electricity Bill Management System (C)**

---

## **Modules Used**

| Module          | Purpose                               |
| --------------- | ------------------------------------- |
| main.c          | Controls overall program flow         |
| consumer.h/.c   | Defines consumer data structure       |
| input.h/.c      | Takes user input                      |
| validation.h/.c | Validates input data                  |
| billing.h/.c    | Calculates electricity bill           |
| output.h/.c     | Displays and saves bill               |
| utils.h/.c      | Helper functions (date, screen, etc.) |

---

## **Consumer Structure**

```
typedef struct {
    name, phone, service_number, category;
    prev_reading, curr_reading;
    units, amount, total_amount;
    bill_date, due_date;
} Consumer;
```

---

## **Main Flow**

```
main()
 → get input
 → validate data
 → calculate bill
 → display bill
 → save to file
```

---

## **Module Responsibilities**

* **Input Module** → Get consumer details and service numbers
* **Validation Module** → Check name, phone, service number, readings
* **Billing Module** → Apply slab rates and calculate amount
* **Output Module** → Show bill and store in file
* **Utils Module** → Date calculation and screen handling

---

## **Slab Rates**

* Domestic: 0–50:1.5, 51–100:2.5, 101–150:3.5, >150:4.5
* Commercial: +1 to each rate
* Industrial: +2 to each rate
* Minimum bill: ₹25

---

## **Data Flow**

```
Input → Validation → Billing → Output → File Save
```

---

## **Memory**

* Uses stack memory only
* No dynamic allocation

---

This is **compact, simple, and perfect** for Word submission.
