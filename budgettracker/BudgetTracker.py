class BudgetTracker:
    def __init__(self):
        self.balance = 0
        self.transactions = []

    def add_income(self, amount, description):
        self.balance += amount
        self.transactions.append({"type": "Income", "amount": amount, "description": description})
        print(f"Income added: {description} - ${amount}")

    def add_expense(self, amount, description):
        self.balance -= amount
        self.transactions.append({"type": "Expense", "amount": amount, "description": description})
        print(f"Expense added: {description} - ${amount}")

    def show_balance(self):
        print(f"Current balance: ${self.balance}")

    def show_transactions(self):
        for transaction in self.transactions:
            print(f"{transaction['type']}: {transaction['description']} - ${transaction['amount']}")


def main():
    budget = BudgetTracker()
    while True:
        print("\nBudget Tracker")
        print("1. Add Income")
        print("2. Add Expense")
        print("3. View Balance")
        print("4. View Transactions")
        print("5. Exit")
        choice = input("What would you like to do? ")

        if choice == "1":
            description = input("Description: ")
            amount = float(input("Amount: "))
            budget.add_income(amount, description)
        elif choice == "2":
            description = input("Description: ")
            amount = float(input("Amount: "))
            budget.add_expense(amount, description)
        elif choice == "3":
            budget.show_balance()
        elif choice == "4":
            budget.show_transactions()
        elif choice == "5":
            print("Exiting Budget Tracker.")
            break
        else:
            print("Invalid choice, please try again.")

if __name__ == "__main__":
    main()
