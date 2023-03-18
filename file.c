
#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <sys/stat.h>
#include <string.h>
#include <ctype.h>
#include <pwd.h>
#include <limits.h>
#include <regex.h>
#include <errno.h>

#define MY_TMP_FILE_TEMPLATE "/tmp/file_XXXXXXXXXX"

int main(int argc, char* argv[])
{
    int tmpFileFd = mkstemp(MY_TMP_FILE_TEMPLATE);
    if (tmpFileFd == -1) {
        perror("Error creating temporary file");
        return EXIT_FAILURE;
    }

    FILE *tmpFile = fdopen(tmpFileFd, "w");
    if (tmpFile == NULL) {
        perror("Error opening temporary file");
        close(tmpFileFd);
        return EXIT_FAILURE;
    }

    // Validate user input
    char input[256];
    if (argc > 1) {
        regex_t regex;
        int reti;
        reti = regcomp(&regex, "^[A-Za-z0-9_]+$", 0);
        if (reti) {
            printf("Could not compile regex\n");
            fclose(tmpFile);
            return EXIT_FAILURE;
        }
        reti = regexec(&regex, argv[1], 0, NULL, 0);
        if (!reti) {
            strncpy(input, argv[1], sizeof(input) - 1);
            input[sizeof(input) - 1] = 0;
            if (fputs(input, tmpFile) == EOF) {
                perror("Error writing to temporary file");
                fclose(tmpFile);
                regfree(&regex);
                return EXIT_FAILURE;
            }
        } else if (reti == REG_NOMATCH) {
            printf("Invalid input!\n");
            fclose(tmpFile);
            regfree(&regex);
            return EXIT_FAILURE;
        } else {
            printf("Regex match failed\n");
            fclose(tmpFile);
            regfree(&regex);
            return EXIT_FAILURE;
        }
        regfree(&regex);
    } else {
        if (fputs("Some text...\n", tmpFile) == EOF) {
            perror("Error writing to temporary file");
            fclose(tmpFile);
            return EXIT_FAILURE;
        }
    }

    // Set file permissions
    struct passwd *pw = getpwuid(getuid());
    if (fchown(tmpFileFd, pw->pw_uid, pw->pw_gid) != 0) {
        printf("Error setting file permissions: %s\n", strerror(errno));
        fclose(tmpFile);
        return EXIT_FAILURE;
    }
    if (fchmod(tmpFileFd, S_IRUSR | S_IWUSR) != 0) {
        printf("Error setting file permissions: %s\n", strerror(errno));
        fclose(tmpFile);
        return EXIT_FAILURE;
    }

    fclose(tmpFile);
    unlink(MY_TMP_FILE_TEMPLATE);
    return EXIT_SUCCESS;
}
