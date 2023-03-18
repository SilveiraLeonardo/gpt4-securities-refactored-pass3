
#include <stdio.h>
#include <stdlib.h>
#include <string.h>

enum { BUFFER_SIZE = 50 }; 

int main() {
    char buffer[BUFFER_SIZE];
    volatile int check = 0; // use 'volatile' to help protect the check variable from potential optimization-based security issues

    // Initialize the buffer to all null bytes
    memset(buffer, 0, BUFFER_SIZE);

    // Copy the string to the buffer, safely avoiding buffer overflow
    strncpy(buffer, "This string does not meant anything ...", BUFFER_SIZE - 1);

    // Manually add a null-terminating character to ensure the buffer is properly terminated
    buffer[BUFFER_SIZE - 1] = '\0';

    // Check if the buffer contains the expected string and set the 'check' variable accordingly
    if (strncmp(buffer, "This string does not meant anything ...", BUFFER_SIZE - 1) == 0) {
        check = 1;
    }

    printf("check: %d", check);

    return EXIT_SUCCESS;
}
