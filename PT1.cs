
using Microsoft.AspNetCore.Mvc;
using System.IO;
using System.Linq;
using System;

namespace WebFox.Controllers.PathTraversal
{
    public class PathTraversalTest1 : ControllerBase
    {
        [HttpDelete("{path}")]
        public void Test(string path)
        {
            // Validate path and file name immediately
            if (Path.GetInvalidPathChars().Any(c => path.Contains(c)) || Path.GetInvalidFileNameChars().Any(c => path.Contains(c)))
            {
                throw new ArgumentException("Invalid path or file name");
            }

            string rootPath = Path.Combine(Directory.GetCurrentDirectory(), "files");
            string fullPath = Path.GetFullPath(Path.Combine(rootPath, Path.GetRelativePath(rootPath, path)));

            if (Path.GetFullPath(fullPath).StartsWith(rootPath) && System.IO.File.Exists(fullPath))
            {
                string fileName = Path.GetFileName(fullPath);

                // Check if user has required role and matches the file owner
                if (fileName.StartsWith("file_") && fileName.EndsWith(".txt") && User.IsInRole("Admin") &&
                    User.Identity.Name == Path.GetFileNameWithoutExtension(fileName).Substring(5))
                {
                    if (Path.GetDirectoryName(fullPath) == rootPath)
                    {
                        FileAttributes attributes = File.GetAttributes(fullPath);

                        // Check if the file is not a symlink
                        if ((attributes & FileAttributes.ReparsePoint) == 0)
                        {
                            // Check if the file is not a directory
                            if (!Directory.Exists(fullPath))
                            {
                                System.IO.File.Delete(fullPath);
                            }
                            else
                            {
                                throw new UnauthorizedAccessException("User is not authorized to delete the directory");
                            }
                        }
                        else
                        {
                            throw new UnauthorizedAccessException("User is not authorized to delete the file");
                        }
                    }
                    else
                    {
                        throw new UnauthorizedAccessException("User is not authorized to delete the file");
                    }
                }
                else
                {
                    throw new UnauthorizedAccessException("User is not authorized to delete the file");
                }
            }
            else
            {
                throw new ArgumentException("Invalid path");
            }
        }
    }
}
