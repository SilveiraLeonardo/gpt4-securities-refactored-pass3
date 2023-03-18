
using System.IO;
using System.Text.RegularExpressions;
using Microsoft.AspNetCore.Mvc;

namespace WebFox.Controllers.PathTraversal
{
    public class PathTraversalTest3 : ControllerBase
    {
        private const string RootFolder = @"C:\Temp\Data\";

        // Add a white-list pattern for allowed user inputs
        private const string AllowedUserInputPattern = @"^[a-zA-Z0-9-_]+(\.[a-zA-Z0-9]+)?$";
        
        [HttpGet("{userInput}")]
        public void Test([FromRoute]string userInput)
        {
            // Validate user input based on the white-list pattern
            if (!Regex.IsMatch(userInput, AllowedUserInputPattern))
            {
                return;
            }
            
            string[] lines = { "First line", "Second line", "Third line" };
            string filePath = Path.Combine(RootFolder, Path.GetFileName(userInput));

            // Validate user input ensuring it's inside the RootFolder directory
            if (Path.GetFullPath(Path.Combine(RootFolder, userInput)).IndexOf(RootFolder, StringComparison.OrdinalIgnoreCase) < 0)
            {
                return;
            }

            // Check file permissions
            if (!File.Exists(filePath) || (File.Exists(filePath) && (File.GetAttributes(filePath) & FileAttributes.ReadOnly) != FileAttributes.ReadOnly))
            {
                using (var outputFile = new FileStream(filePath, FileMode.OpenOrCreate, FileAccess.Write, FileShare.Read))
                {
                    using (var writer = new StreamWriter(outputFile))
                    {
                        foreach (var line in lines)
                            writer.WriteLine(line);
                    }
                }
            }
        }
    }
}
