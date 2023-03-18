
using System.IO;
using Microsoft.AspNetCore.Mvc;
using System;

namespace WebFox.Controllers.PathTraversal
{
    public class PathTraversalTest4 : ControllerBase
    {
        private const string RootFolder = @"C:\Temp\Data\";
        private const string AllowedExtension = ".txt";

        // Function to validate the input for path traversal vulnerability
        private static bool IsValidFileName(string userInput)
        {
            string[] invalidChars = { "..", "/", "\\" };
            foreach (var invalid in invalidChars)
            {
                if (userInput.Contains(invalid)) return false;
            }
            return true;
        }

        [HttpGet("{userInput}")]
        public IActionResult Test(string userInput)
        {
            if (string.IsNullOrWhiteSpace(userInput) || !IsValidFileName(userInput))
            {
                return BadRequest("Invalid input");
            }

            // Sanitize user input
            var fileName = Path.GetFileName(userInput);
            var filePath = Path.Combine(RootFolder, fileName);

            if (!File.Exists(filePath))
            {
                return NotFound("File not found");
            }

            // Validate file extension
            if (!Path.GetExtension(filePath).Equals(AllowedExtension, StringComparison.OrdinalIgnoreCase))
            {
                return BadRequest("Invalid file extension");
            }

            // Validate file name
            if (!Path.GetFileName(filePath).Equals("test.txt", StringComparison.OrdinalIgnoreCase))
            {
                return BadRequest("Invalid file name");
            }

            string[] lines = { "First line", "Second line", "Third line" };
            using var outputFile = new StreamWriter(filePath, append: false);
            foreach (var line in lines)
            {
                outputFile.WriteLine(line);
            }

            return Ok("File updated successfully");
        }
    }
}
