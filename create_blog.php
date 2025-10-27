<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Create Blog</title>
  <link rel="stylesheet" href="create_blog.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
</head>
<body>

<div class="container">
  <form action="publish_blog.php" method="POST" enctype="multipart/form-data">
    
    <!-- Tools Panel -->
    <div class="tools">
      <h2>Tools</h2>
      <button type="button" onclick="insertCode()"><i class="fas fa-code"></i> Insert Code from File</button>
      <button type="button" onclick="selectFile('image')"><i class="fas fa-image"></i> Insert Image</button>
      <button type="button" onclick="selectFile('audio')"><i class="fas fa-microphone"></i> Insert Audio</button>
      <button type="button" onclick="selectFile('video')"><i class="fas fa-video"></i> Insert Video</button>
    </div>

    <!-- Editor Panel -->
    <div class="editor">
      <input type="text" name="title" placeholder="Enter Blog Title" required />
      <input type="text" name="author" placeholder="Author" required />
      <input type="text" name="summary" placeholder="Enter a short summary" />

      <div id="content" contenteditable="true">Start writing your blog...</div>

      <input type="hidden" name="content" id="hiddenContent" />
      <input type="file" id="mediaInput" style="display: none;" />

      <button type="submit" onclick="save()">Publish</button>
    </div>

    <!-- Live Preview -->
    <div class="preview">
      <h3>Live Preview</h3>
      <div id="previewBox">Preview will appear here...</div>
    </div>
  </form>
</div>

<!-- Floating Formatting Toolbar -->
<div id="formattingToolbar">
  <button onclick="formatText('bold')"><i class="fas fa-bold"></i></button>
  <button onclick="formatText('italic')"><i class="fas fa-italic"></i></button>
  <button onclick="formatText('underline')"><i class="fas fa-underline"></i></button>
</div>

<script>
// Save blog content before submission
function save() {
  document.getElementById("hiddenContent").value = document.getElementById("content").innerHTML;
}

// Insert code from local file
function insertCode() {
  const input = document.createElement("input");
  input.type = "file";
  input.accept = ".txt,.js,.py,.java,.cpp,.c,.html,.css,.php";

  input.onchange = function () {
    const file = input.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
      const pre = document.createElement("pre");
      pre.textContent = e.target.result;
      document.getElementById("content").appendChild(pre);
    };
    reader.readAsText(file);
  };
  input.click();
}

// Select and insert media (image, audio, video)
function selectFile(type) {
  const input = document.getElementById("mediaInput");
  input.accept = type + "/*";
  input.onchange = function () {
    const file = input.files[0];
    if (!file) return;

    const url = URL.createObjectURL(file);
    let element;
    if (type === "image") {
      element = document.createElement("img");
      element.src = url;
      element.style.maxWidth = "100%";
    } else if (type === "audio") {
      element = document.createElement("audio");
      element.src = url;
      element.controls = true;
    } else if (type === "video") {
      element = document.createElement("video");
      element.src = url;
      element.controls = true;
      element.width = 300;
    }
    document.getElementById("content").appendChild(element);
    updatePreview(); // Update preview after adding media
  };
  input.click();
}

// Basic text formatting
function formatText(command) {
  document.execCommand(command, false, null);
  updatePreview();
}

// Show floating toolbar
document.getElementById("content").addEventListener("mouseup", function () {
  const selection = window.getSelection();
  const toolbar = document.getElementById("formattingToolbar");

  if (selection.toString()) {
    const rect = selection.getRangeAt(0).getBoundingClientRect();
    toolbar.style.top = rect.top + window.scrollY - 40 + "px";
    toolbar.style.left = rect.left + window.scrollX + "px";
    toolbar.style.display = "block";
  } else {
    toolbar.style.display = "none";
  }
});

// Live preview on typing
document.getElementById("content").addEventListener("input", function () {
  updatePreview();
});

// On page load, initialize preview
window.addEventListener("DOMContentLoaded", () => {
  updatePreview();
});

// Update preview content
function updatePreview() {
  document.getElementById("previewBox").innerHTML = document.getElementById("content").innerHTML;
}
</script>

</body>
</html>
