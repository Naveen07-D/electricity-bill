# **Logic & Algorithm**

## **Electricity Bill Management System (C)**

---

## **Program Flow**

1. Start program
2. Get consumer details (name, phone, service no, category, readings)
3. Validate inputs
4. Calculate units and bill using slab rates
5. Generate bill date and due date (+15 days)
6. Display bill
7. Save to file
8. End program

---

## **Input Validation**

* Name → alphabets only
* Phone → exactly 10 digits
* Service number → must be unique
* Current reading > Previous reading

---

## **Bill Calculation**

```
units = current - previous

If units = 0 → ₹25

Domestic Slabs:
0–50 → 1.5/unit
51–100 → 2.5/unit
101–150 → 3.5/unit
>150 → 4.5/unit

Commercial → +1 to each rate
Industrial → +2 to each rate
```

---

## **Date Logic**

* Bill date = today
* Due date = today + 15 days

---

## **File Handling**

* Save bill details in "bills_history.txt"

---

## **Data Structure**

```
struct Consumer {
    name, phone, service_no, category,
    prev_reading, curr_reading,
    units, amount, bill_date, due_date
};
```

---

## **Error Handling**

* Invalid input → show error and retry
* File error → show message

---

## **Memory**

* Stack memory
* No dynamic allocation

---

## **Exit**

* Success → return 0
* Error → return 1

---
