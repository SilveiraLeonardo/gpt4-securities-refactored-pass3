
#include <string.h>
#include <sys/types.h>
#include <sys/socket.h>
#include <netdb.h>
#include <unistd.h>
#include <ctype.h>

static void env_fix_display(void) {
    enviro *ep = env_find("DISPLAY");
    if (!ep) return;
    ep->setexport(1);
    if (strncmp(ep->getval(), ":", 1) && strncmp(ep->getval(), "UNIX", 5)) {
        return;
    }

    char hbuf[256];
    const char *cp2 = strrchr(ep->getval(), ':');
    int maxlen = sizeof(hbuf) - strlen(cp2) - 1;
    if (gethostname(hbuf, maxlen) != 0) {
        return;
    }
    hbuf[sizeof(hbuf) - 1] = 0;

    if (!strchr(hbuf, '.')) {
        struct addrinfo hints, *res;
        int err;
        memset(&hints, 0, sizeof(hints));
        hints.ai_family = AF_INET;
        hints.ai_socktype = SOCK_STREAM;
        hints.ai_flags = AI_ADDRCONFIG;
        if ((err = getaddrinfo(hbuf, NULL, &hints, &res)) == 0) {
            if (res->ai_canonname) {
                strlcpy(hbuf, res->ai_canonname, sizeof(hbuf));
            }
            freeaddrinfo(res);
        }
    }

    // Validate and sanitize the input.
    size_t i;
    for (i = 0; hbuf[i] != '\0'; ++i) {
        if (!isalnum(hbuf[i]) && hbuf[i] != '.' && hbuf[i] != '-') {
            return;
        }
    }

    // Check the length of the input.
    size_t hbuf_len = strlen(hbuf);
    size_t cp2_len = strlen(cp2);
    if (hbuf_len > 0 && hbuf_len < maxlen && hbuf_len + cp2_len < sizeof(hbuf)) {
        // Escape the input
        char escaped_hbuf[sizeof(hbuf)] = {0};
        size_t j = 0;
        for (i = 0; i < hbuf_len; ++i) {
            if (hbuf[i] != ';' && hbuf[i] != '&' && hbuf[i] != '|') {
                escaped_hbuf[j++] = hbuf[i];
            }
        }

        strlcat(escaped_hbuf, cp2, sizeof(escaped_hbuf));
        if (strncmp(escaped_hbuf, ep->getval(), maxlen) != 0) {
            ep->define("DISPLAY", escaped_hbuf);
        }
    }
}
