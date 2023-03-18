
#include <stdio.h>
#include <string.h>
#include <limits.h>

#define S 100
#define N 1000

int main(int argc, char *argv[]) {
  char out[S];
  char buf[N];
  char msg[] = "Welcome to the argument echoing program\n";
  int len = 0;
  buf[0] = '\0';
  printf("%s", msg);
  while (argc) {
    size_t argv_len = strlen(argv[argc-1]);
    if (argv_len < S - 16 && argv_len > 0 && strlen(buf) < N - argv_len) {
      if (strspn(argv[argc-1], "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ") == argv_len) {
        snprintf(out, S - 16, "argument %d is %s\n", argc-1, argv[argc-1]);
        argc--;
        strncat(buf, out, N - len - 2);
        len = strlen(buf);
      } else {
        printf("Invalid input\n");
        return 1;
      }
    } else {
      printf("Input too long\n");
      return 1;
    }
  }
  printf("%s", buf);
  return 0;
}
