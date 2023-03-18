
import java.io.*;
import java.nio.file.Files;
import java.nio.file.attribute.AclFileAttributeView;
import java.nio.file.attribute.PosixFilePermission;
import java.nio.file.attribute.PosixFilePermissions;
import java.security.NoSuchAlgorithmException;
import java.util.ArrayList;
import java.util.List;
import java.util.Set;
import java.util.UUID;

import javax.crypto.Cipher;
import javax.crypto.KeyGenerator;
import javax.crypto.SecretKey;

class LogFile implements Serializable {
    private static final long serialVersionUID = 1L;
    private String filename;
    private String filecontent;

    // Add your secure file content validation or sanitization logic here
    private void validateFileContent(String filecontent) {
        // Example: Check if file content length is within a limit
        if (filecontent.length() > 100000) {
            throw new IllegalArgumentException("File content is too large");
        }
    }

    private void setFilePermissions(File file, String permissions) throws IOException {
        if (System.getProperty("os.name").toLowerCase().contains("win")) { // Windows file permissions
            AclFileAttributeView view = Files.getFileAttributeView(file.toPath(), AclFileAttributeView.class);
            // Add required Windows-specific file permission settings here
        } else { // POSIX file permissions
            Set<PosixFilePermission> posixPermissions = PosixFilePermissions.fromString(permissions);
            Files.setPosixFilePermissions(file.toPath(), posixPermissions);
        }
    }

    private void validateFilename(String filename) {
        if (!filename.matches("[a-zA-Z0-9_\\-\\.]+") || filename.contains("../") || filename.contains("..\\\\")) {
            throw new IllegalArgumentException("Invalid filename");
        }
    }

    private void readObject(ObjectInputStream in) {
        System.out.println("readObject from LogFile");

        try {
            in.defaultReadObject();
            System.out.println("File name: " + filename + ", file content: \n" + filecontent);

            validateFilename(filename);
            validateFileContent(filecontent);

            File file = new File(filename);
            setFilePermissions(file, "rw-------");

            String encryptedFilename = saveEncryptedContent(filecontent);

            System.out.println("Encrypted content saved to: " + encryptedFilename);
        } catch (IOException | ClassNotFoundException e) {
            System.out.println("Error: " + e.getMessage());
            e.printStackTrace();
        }
    }

    private String saveEncryptedContent(String filecontent) throws IOException {
        byte[] encryptedContent = encryptContent(filecontent);

        String encryptedFilename = UUID.randomUUID().toString() + ".enc";

        File encryptedFile = new File(encryptedFilename);
        Files.write(encryptedFile.toPath(), encryptedContent);

        // Set secure file permissions
        setFilePermissions(encryptedFile, "rw-------");

        return encryptedFilename;
    }

    private byte[] encryptContent(String filecontent) {
        try {
            KeyGenerator keyGen = KeyGenerator.getInstance("AES");
            keyGen.init(128);
            SecretKey secretKey = keyGen.generateKey();

            Cipher cipher = Cipher.getInstance("AES/GCM/NoPadding");

            cipher.init(Cipher.ENCRYPT_MODE, secretKey);
            return cipher.doFinal(filecontent.getBytes());
        } catch (Exception e) {
            throw new IllegalStateException("Error encrypting file content", e);
        }
    }
}
