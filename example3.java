
import android.content.Intent;
import android.os.Environment;
import android.util.Log;
import java.io.File;
import java.io.IOException;

public class MainActivity extends AppCompatActivity { // Assuming this code is inside an Activity class

    // ...

    private void processPath() {
        Intent in = getIntent();
        String path = in.getStringExtra("path");
        if (path == null) {
            Log.e(TAG, "Attempt to write to invalid path");
            return;
        }
        // Sanitize the path
        String sanitizedPath = sanitizePath(path);

        // Restrict the path to a specific, app-specific directory
        File basePath = new File(getExternalFilesDir(null), "/myapp/");
        File targetFile = new File(basePath, sanitizedPath);
        try {
            targetFile = targetFile.getCanonicalFile();
        } catch (IOException e) {
            Log.e(TAG, "Error processing the path: " + e.getMessage());
            return;
        }

        if (!targetFile.getPath().startsWith(basePath.getPath())) {
            Log.e(TAG, "Attempt to write to invalid path");
            return;
        }

        writeToFile(targetFile.getPath()); // Method that actually writes the data.
    }

    private String sanitizePath(String path) {
        String[] allowedDirectories = {"subdir1", "subdir2", "subdir3"}; // Whitelist of allowed subdirectories.
        String[] splittedPath = path.split(File.separator);
        
        StringBuilder sanitizedPath = new StringBuilder();
        for (String dir : splittedPath) {
            String sanitizedDir = dir.replaceAll("[^a-zA-Z0-9_-]", "");
            if (Arrays.asList(allowedDirectories).contains(sanitizedDir)) {
                sanitizedPath.append(sanitizedDir).append(File.separator);
            }
        }
        
        return sanitizedPath.toString();
    }

}
