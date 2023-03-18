
#include <stdio.h>
#include <string.h>
#include <limits.h>
#include <ctype.h>

#define MAX_USERNAME_LENGTH 8

int validateInput(char *username, int username_length);
int isValidUsername(char *username, int username_length);
int isValidChar(char *username, int username_length);
int grantAccess(char *username, int username_length);
void privilegedAction();

int main () {
    char username[MAX_USERNAME_LENGTH + 1]; // +1 for the null terminator
    int allow = 0;

    printf("Enter your username, please: ");
    if (fgets(username, sizeof(username), stdin) != NULL) {
        username[strcspn(username, "\n")] = 0; // remove newline
        int username_length = strlen(username);

        if (validateInput(username, username_length) && grantAccess(username, username_length)) {
            allow = 1;
        }
    }
    if (allow != 0) {
        privilegedAction();
    }
    return 0;
}

int validateInput(char *username, int username_length) {
    // Validate the username here
    if (username_length > 0 && username_length <= MAX_USERNAME_LENGTH && isValidUsername(username, username_length)) {
        return 1;
    }
    return 0;
}

int isValidUsername(char *username, int username_length) {
    // Check if the username is valid
    if (strcmp(username, "admin") != 0 && username_length <= MAX_USERNAME_LENGTH && isValidChar(username, username_length)) {
        return 1;
    }
    return 0;
}

int isValidChar(char *username, int username_length) {
    // Check if the username contains only valid characters
    for (int i = 0; i < username_length; i++) {
        if (!isalpha(username[i]) || username[i] == '\0') {
            return 0;
        }
    }
    return 1;
}

int grantAccess(char *username, int username_length) {
    // Check if the user has the necessary privileges
    if (strcmp(username, "allowed_user") == 0) {
        return 1;
    }
   return 0;
}

void privilegedAction() {
    // Perform privileged action here
    printf("Access granted. Performing privileged action...\n");
}
