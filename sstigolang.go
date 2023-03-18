
package main

import (
	"bufio"
	"errors"
	"fmt"
	"log"
	"os"
	"strings"
)

const maxInputSize = 1024

func main() {
	reader := bufio.NewReader(os.Stdin)
	text, err := ReadStringWithLimit(reader, '\n', maxInputSize)
	if err != nil {
		log.Printf("ReadStringWithLimit: %v", err)
		return
	}

	// Validate the input
	if !strings.HasPrefix(text, "echo") {
		return
	}

	// Don't execute the command. Just return the input with 'echo' removed.
	fmt.Println(strings.TrimSpace(text[4:]))
}

func ReadStringWithLimit(r *bufio.Reader, delim byte, limit int) (string, error) {
	var result strings.Builder
	for {
		b, err := r.ReadByte()
		if err != nil || b == delim {
			return result.String(), err
		}
		if result.Len() <= limit {
			result.WriteByte(b)
		} else {
			return "", errors.New("input size limit exceeded")
		}
	}
}
