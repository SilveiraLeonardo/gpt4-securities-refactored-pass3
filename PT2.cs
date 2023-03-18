
using System;
using System.IO;
using System.Text.RegularExpressions;
using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Authorization;

namespace WebFox.Controllers.PathTraversal
{
    [Authorize] // Requires authentication
    public class PathTraversalTest2 : ControllerBase
    {
        private const string RootFolder = @"C:\Temp\Data\";
        private const string AllowedExtension = ".txt";
        private const int MaxFileSize = 1024 * 1024; // 1 MB
        private static readonly Regex AllowedCharsPattern = new Regex("^[a-zA-Z0-9-_]+$");

        [HttpDelete("{userInput}")]
        public IActionResult Test(string userInput)  
        {
            try
            {
                // Validate user input
                if (string.IsNullOrWhiteSpace(userInput) || !AllowedCharsPattern.IsMatch(userInput))
                {
                    throw new ArgumentException("Invalid path");
                }
                
                // Create a secure path
                var fullPath = Path.GetFullPath(Path.Combine(RootFolder, Path.GetFileName(userInput)));

                // Validate file path
                if (!fullPath.StartsWith(RootFolder))
                {
                    throw new ArgumentException("Invalid path");
                }

                // Validate file extension
                if (!Path.GetExtension(fullPath).Equals(AllowedExtension, StringComparison.OrdinalIgnoreCase))
                {
                    throw new ArgumentException("Invalid file extension");
                }

                // Validate file size
                if (new FileInfo(fullPath).Length > MaxFileSize)
                {
                    throw new ArgumentException("File size too large");
                }

                // Validate file content
                if (!ValidateFileContent(fullPath))
                {
                    throw new ArgumentException("Invalid file content");
                }

                // Validate file permissions
                if (!ValidateFilePermissions(fullPath))
                {
                    throw new ArgumentException("Invalid file permissions");
                }

                System.IO.File.Delete(fullPath);

                return Ok("File deleted successfully.");
            }    
            catch (IOException)    
            {
                return BadRequest("IOException: File operation error.");
            }
            catch (ArgumentException)
            {
                return BadRequest("ArgumentException: Invalid file operation.");
            }
        }

        private bool ValidateFileContent(string fullPath)
        {
            // Validate file content
            using (var reader = new StreamReader(fullPath))
            {
                var content = reader.ReadToEnd();
                var regex = new Regex(@"<\s*(script|style)", RegexOptions.IgnoreCase);
                if (regex.IsMatch(content))
                {
                    return false;
                }
            }
            return true;
        }

        private bool ValidateFilePermissions(string fullPath)
        {
            // Validate file permissions
            var fileSecurity = File.GetAccessControl(fullPath);
            if (fileSecurity.AreAccessRulesProtected)
            {
                return false;
            }
            // Further permission validations can be added as per the requirements

            return true;
        }
    }
}
