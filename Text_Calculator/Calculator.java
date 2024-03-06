import java.util.Scanner;

public class Calculator {
    public static void main(String[] args) {
        //stores numeric variables and operator character
        double num1, num2;
        double result;
        char operation;

        //takes the user input
        Scanner userInput = new Scanner(System.in);
        System.out.println("Enter the values: ");

        //taking users number inputs
        num1 = userInput.nextInt();
        num2 = userInput.nextInt();

       //taking users operator input
        System.out.println("Enter your operator: (+,-,*,/)");
        Scanner userOp = new Scanner(System.in);

        //takes the previous input and run it through the switch statement based on the operator chosen
        operation = userOp.next().charAt(0);
        switch (operation) {
            case '+' -> result = num1 + num2;
            case '-' -> result = num1 - num2;
            case '*' -> result = num1 * num2;
            case '/' -> result = num1 / num2;
            default -> {
                System.out.println("Error! Invalid value");
                return;
            }
        }
        System.out.println("The answer is: ");
        System.out.println(result);


    }
}
