
#include <stdio.h>
#include <string.h>
#include <stdlib.h>

#define MAX_INPUT_LEN 1024

int main(int argc, char **argv) {
    if (argc < 2) {
        printf("No input provided\n");
        return 0;
    }

    size_t input_len = strnlen(argv[1], MAX_INPUT_LEN + 1);
    if (input_len > MAX_INPUT_LEN) {
        printf("Input too long\n");
        return 0;
    }

    char *input = malloc(input_len + 1);
    if (input == NULL) {
        printf("Error allocating memory\n");
        return 1;
    }

    // Validate and sanitize input
    for (int i = 0; i < input_len; i++) {
        if (argv[1][i] < 32 || argv[1][i] > 126) {
            printf("Invalid input\n");
            free(input);
            return 0;
        }
    }

    // Use the strncpy_s function if available, otherwise use strlcpy
    #if defined(_MSC_VER) || defined(__STDC_LIB_EXT1__)
        strncpy_s(input, input_len + 1, argv[1], input_len);
    #else
        strlcpy(input, argv[1], input_len + 1);
    #endif
    input[input_len] = '\0';

    printf("%s", input);
    free(input);

    return 0;
}
