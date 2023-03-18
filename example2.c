
#include <iostream>
#include <cstring>
#include <cstdlib>
#include <unistd.h>
#include <limits>
#include <cerrno>

using namespace std;

int main() {
    int i;
    char inLine[64];
    
    // Calculate remaining buffer size and limit input length to prevent buffer overflow
    size_t remaining_buffer = sizeof(inLine) - 1;
    cin.width(remaining_buffer);
    cin >> inLine;

    // Add input validation
    size_t len = strlen(inLine);
    if (len > 0 && len < sizeof(inLine)) {
        for (size_t j = 0; j < len; j++) {
            if (!isdigit(inLine[j])) {
                cout << "Input must be a number!" << endl;
                return 0;
            }
        }
        
        // Use 'strtol' instead of 'atoi' to check for possible conversion errors
        char *end;
        errno = 0;
        long int converted_num = strtol(inLine, &end, 10);
        if (errno == ERANGE || *end != '\0') {
            cout << "Invalid number!" << endl;
            return 0;
        }
        
        i = static_cast<int>(converted_num);
        if (i > 0 && i < 60) {
            if (len == 1) {
                // Limit sleep time to a reasonable maximum (e.g., 10 seconds) to prevent possible DoS attacks
                sleep(min(i, 10));
            }
        } else {
            cout << "Input must be between 1 and 59!" << endl;
        }
    } else {
        cout << "Input too long!" << endl;
    }

    return 0;
}
