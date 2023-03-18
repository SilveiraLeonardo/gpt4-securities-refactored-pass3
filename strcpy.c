
#include <string.h>
#include <stdio.h>

#define BUFFER_SIZE 20

int main() {
    char str1[BUFFER_SIZE];          // Declare a buffer with a defined size
    char str2[] = "abcdefghijklmn";  // Initialize another string
    size_t len = strlen(str2);       // Get the length of str2

    if (len > BUFFER_SIZE - 1) {       // Check if the length is greater than allowed
        len = BUFFER_SIZE - 1;         // Set length to be the maximum allowed length
    }

    strncpy(str1, str2, len);         // Copy len characters from str2 to str1
    str1[len] = '\0';                 // Make sure to add null terminator to str1

    printf("%s\n", str1);             // Print the result

    return 0;
}
