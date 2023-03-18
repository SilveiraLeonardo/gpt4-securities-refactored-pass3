
using System;
using System.IO;
using System.IO.Compression;
using System.Security.AccessControl;
using System.Security.Principal;
using System.Linq;

namespace myApp
{
    class Program
    {
        static void Main(string[] args)
        {
            Console.WriteLine("Enter Path of Zip File to extract:");
            string zipPath = Console.ReadLine();
            Console.WriteLine("Enter Path of Destination Folder");
            string extractPath = Console.ReadLine();

            // Validate user input
            if (!File.Exists(zipPath) || !Directory.Exists(extractPath))
            {
                Console.WriteLine("Invalid path!");
                return;
            }

            // Sanitize path
            zipPath = Path.GetFullPath(zipPath);
            extractPath = Path.GetFullPath(extractPath);

            try
            {
                using (ZipArchive archive = ZipFile.OpenRead(zipPath))
                {
                    // Define allowed file extensions
                    string[] allowedExtensions = { ".txt", ".jpg", ".png" };

                    foreach (ZipArchiveEntry entry in archive.Entries)
                    {
                        // Check if the file extension is allowed
                        if (!allowedExtensions.Contains(Path.GetExtension(entry.Name)))
                        {
                            Console.WriteLine($"Skipping file {entry.Name} due to unallowed file type.");
                            continue;
                        }

                        string destinationPath = Path.GetFullPath(Path.Combine(extractPath, entry.FullName));

                        // Check for path traversal attack
                        if (!destinationPath.StartsWith(extractPath, StringComparison.OrdinalIgnoreCase))
                        {
                            Console.WriteLine($"Skipping file {entry.FullName} due to security restrictions.");
                            continue;
                        }

                        // Check for duplicates
                        if (File.Exists(destinationPath))
                        {
                            // Handle duplicate files (e.g. add a unique suffix or skip extraction)
                            continue;
                        }

                        // Set file permissions
                        FileSecurity security = new FileSecurity();
                        security.AddAccessRule(new FileSystemAccessRule(WindowsIdentity.GetCurrent().Name, FileSystemRights.Read, AccessControlType.Allow));
                        security.AddAccessRule(new FileSystemAccessRule(WindowsIdentity.GetCurrent().Name, FileSystemRights.Write, AccessControlType.Allow));

                        // Extract the file
                        entry.ExtractToFile(destinationPath);
                        File.SetAccessControl(destinationPath, security);
                        Console.WriteLine(destinationPath);
                    }
                }
            }
            catch (IOException ex)
            {
                Console.WriteLine($"Error: {ex.Message}");
            }
            catch (UnauthorizedAccessException ex)
            {
                Console.WriteLine($"Error: {ex.Message}");
            }
            catch (NotSupportedException ex)
            {
                Console.WriteLine($"Error: {ex.Message}");
            }
        }
    }
}
