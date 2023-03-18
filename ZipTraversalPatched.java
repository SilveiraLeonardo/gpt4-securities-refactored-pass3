
import java.util.zip.ZipFile;
import java.util.zip.ZipEntry;
import java.nio.file.Paths;
import java.nio.file.Path;
import java.nio.file.Files;
import java.io.File;
import java.io.InputStream;
import java.io.OutputStream;
import java.io.BufferedOutputStream;
import org.apache.commons.io.IOUtils;
import java.util.regex.Pattern;
import java.util.Base64;
import java.security.SecureRandom;
import javax.crypto.Cipher;
import javax.crypto.spec.SecretKeySpec;
import javax.crypto.spec.IvParameterSpec;
import javax.crypto.CipherInputStream;

public class SecureFileExtractor {
    private SecureRandom random = new SecureRandom();

    public void extract(ZipFile zipFile, String toDir, byte[] key) {
        Enumeration entries = zipFile.entries();
        while (entries.hasMoreElements()) {
            ZipEntry zipEntry = entries.nextElement();

            // Validate user input
            if (!Pattern.matches("[a-zA-Z0-9_\\-\\.]+", zipEntry.getName())) {
                throw new SecurityException("Invalid zip entry name!");
            }
            Path basePath = Paths.get(toDir).normalize().toAbsolutePath();
            Path filePath = basePath.resolve(zipEntry.getName()).normalize().toAbsolutePath();
            if (!filePath.startsWith(basePath)){
                throw new SecurityException("ZipEntry not within target directory!");
            }

            try (InputStream istr = zipFile.getInputStream(zipEntry)) {
                InputStream encryptedStream = getEncryptedStream(istr, key);
                String randomFileName = generateRandomFileName(zipEntry.getName());
                File newFile = new File(toDir, randomFileName);
                validateFile(newFile);
                try (OutputStream os = Files.newOutputStream(newFile.toPath());
                     BufferedOutputStream bos = new BufferedOutputStream(os)) {
                    IOUtils.copy(encryptedStream, bos);
                }
            } catch (IOException e) {
                throw new SecurityException("Error handling file: " + zipEntry.getName(), e);
            }
        }
    }

    private InputStream getEncryptedStream(InputStream input, byte[] key) throws IOException {
        try {
            SecretKeySpec keySpec = new SecretKeySpec(key, "AES");
            Cipher cipher = Cipher.getInstance("AES/CBC/PKCS5Padding");
            byte[] iv = new byte[16];
            random.nextBytes(iv);
            IvParameterSpec ivSpec = new IvParameterSpec(iv);
            cipher.init(Cipher.ENCRYPT_MODE, keySpec, ivSpec);
            return new CipherInputStream(input, cipher);
        } catch (Exception e) {
            throw new IOException("Error encrypting stream", e);
        }
    }

    private String generateRandomFileName(String originalName) {
        byte[] bytes = new byte[16];
        random.nextBytes(bytes);
        String randomName = Base64.getUrlEncoder().withoutPadding().encodeToString(bytes);
        if (originalName.contains(".")) {
          String extension = originalName.substring(originalName.lastIndexOf('.'));
          randomName += extension;
        }
        return randomName;
    }

    private void validateFile(File file) {
        // Validate file name
        if (!Pattern.matches("[a-zA-Z0-9_\\-\\.]+", file.getName())) {
            throw new SecurityException("Invalid file name!");
        }
        // Validate file extension
        if (!Pattern.matches("[a-zA-Z0-9_\\-\\.]+\\.(jpg|png|gif|pdf|docx|xlsx|pptx)", file.getName())) {
            throw new SecurityException("Invalid file extension!");
        }
        // Validate file size
        long fileSize = file.length();
        if (fileSize > MAX_FILE_SIZE) {
            throw new SecurityException("File size exceeds maximum allowed!");
        }
    }
}
