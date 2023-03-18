
import java.io.*;
import java.security.SecureRandom;
import java.security.spec.KeySpec;
import javax.crypto.Cipher;
import javax.crypto.SecretKeyFactory;
import javax.crypto.spec.IvParameterSpec;
import javax.crypto.spec.PBEKeySpec;
import javax.crypto.spec.SecretKeySpec;
import java.util.Base64;
import com.fasterxml.jackson.databind.ObjectMapper;

public class Utils {
    private static final int SALT_SIZE = 32;
    private static final int IV_SIZE = 16;
    private static final int ITERATIONS = 65536;
    private static final int KEY_SIZE = 256;

    public static boolean SerializeToFile(Object obj, String filename, String password) {
        if (obj == null || filename == null || filename.isBlank() || password == null || password.isBlank()) {
            return false;
        }
        try (FileOutputStream file = new FileOutputStream(filename);
             ObjectOutputStream out = new ObjectOutputStream(file)) {
            SecureRandom random = new SecureRandom();

            byte[] salt = new byte[SALT_SIZE];
            random.nextBytes(salt);

            byte[] iv = new byte[IV_SIZE];
            random.nextBytes(iv);

            SecretKeySpec secretKey = generateSecretKey(password, salt);

            byte[] encryptedObj = encrypt(obj, secretKey, iv);
            out.writeObject(encryptedObj);
            out.writeObject(salt);
            out.writeObject(iv);

            return true;
        } catch (Exception e) {
            // Log exception here
            return false;
        }
    }

    public static Object DeserializeFromFile(String filename, String password) {
        if (filename == null || filename.isBlank() || password == null || password.isBlank()) {
            return null;
        }
        try (FileInputStream file = new FileInputStream(filename);
             ObjectInputStream in = new ObjectInputStream(file)) {

            byte[] encryptedObj = (byte[]) in.readObject();
            byte[] salt = (byte[]) in.readObject();
            byte[] iv = (byte[]) in.readObject();

            SecretKeySpec secretKey = generateSecretKey(password, salt);

            return decrypt(encryptedObj, secretKey, iv);
        } catch (Exception e) {
            // Log exception here
            return null;
        }
    }

    private static byte[] encrypt(Object obj, SecretKeySpec secretKey, byte[] iv) throws Exception {
        ObjectMapper objectMapper = new ObjectMapper();
        String jsonString = objectMapper.writeValueAsString(obj);

        byte[] plainObj = jsonString.getBytes();

        Cipher cipher = Cipher.getInstance("AES/CBC/PKCS5Padding");
        cipher.init(Cipher.ENCRYPT_MODE, secretKey, new IvParameterSpec(iv));
        return cipher.doFinal(plainObj);
    }

    private static Object decrypt(byte[] encryptedObj, SecretKeySpec secretKey, byte[] iv) throws Exception {
        Cipher cipher = Cipher.getInstance("AES/CBC/PKCS5Padding");
        cipher.init(Cipher.DECRYPT_MODE, secretKey, new IvParameterSpec(iv));
        byte[] plainObj = cipher.doFinal(encryptedObj);

        ObjectMapper objectMapper = new ObjectMapper();
        return objectMapper.readValue(plainObj, Object.class);
    }

    private static SecretKeySpec generateSecretKey(String password, byte[] salt) throws Exception {
        SecretKeyFactory factory = SecretKeyFactory.getInstance("PBKDF2WithHmacSHA256");
        KeySpec keySpec = new PBEKeySpec(password.toCharArray(), salt, ITERATIONS, KEY_SIZE);
        return new SecretKeySpec(factory.generateSecret(keySpec).getEncoded(), "AES");
    }
}
