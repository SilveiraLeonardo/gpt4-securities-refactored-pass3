
import java.util.zip.ZipFile;
import java.util.zip.ZipEntry;
import java.nio.file.Paths;
import java.nio.file.Files;
import java.nio.file.Path;
import java.io.File;
import java.io.InputStream;
import java.io.OutputStream;
import java.io.BufferedOutputStream;
import org.apache.commons.io.IOUtils;
import java.util.UUID;
import java.nio.file.attribute.PosixFilePermission;
import java.util.Set;
import java.util.HashSet;

public void extract(ZipFile zip) {
    try {
        String toDir = "/my/target/directory/";
        Path targetDir = Paths.get(toDir);
        if (!Files.exists(targetDir)) {
            Files.createDirectories(targetDir);
        }
        Enumeration entries = zip.entries();
    
        while (entries.hasMoreElements()) {
            ZipEntry zipEntry = entries.nextElement();
            
            if (!zipEntry.getName().matches("^[a-zA-Z0-9_/\\-]+$")) {
                throw new IllegalArgumentException("Invalid path");
            }
            String sanitizedName = zipEntry.getName().replaceAll("[^a-zA-Z0-9_/\\-]", "");
            File sanitizedFile = new File(toDir, sanitizedName);
            
            File uniqueFile = sanitizedFile;
            if (sanitizedFile.exists()) {
                // Create a unique file name
                String uniqueName = UUID.randomUUID().toString() + "_" + sanitizedName;
                uniqueFile = new File(toDir, uniqueName);
            }
            Path canonicalPath = targetDir.resolve(uniqueFile.toPath()).normalize();
            
            if (canonicalPath.startsWith(targetDir)) {
                if (!uniqueFile.getName().matches("^[a-zA-Z0-9_/\\-]+$")) {
                    throw new IllegalArgumentException("Invalid file name");
                }
    
                Set<PosixFilePermission> perms = new HashSet<>();
                perms.add(PosixFilePermission.OWNER_READ);
                perms.add(PosixFilePermission.OWNER_WRITE);
                // Add other permissions if necessary for your use case
                Files.setPosixFilePermissions(uniqueFile.toPath(), perms);
    
                try (InputStream istr = zip.getInputStream(zipEntry);
                     OutputStream os = Files.newOutputStream(uniqueFile.toPath());
                     BufferedOutputStream bos = new BufferedOutputStream(os)) {
                    IOUtils.copy(istr, bos);
                }
            }
        }
    } catch (IOException | IllegalStateException e) {
        // Handle the exceptions
        System.err.println("Error: " + e.getMessage());
    }
}
