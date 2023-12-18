<?php
// セッションの開始
session_start();

// データベースに接続
$host = "todo_db_1";
$user = "root";
$password = "root_password";
$database = "todo_list_app";

// タスク一覧の取得
$sql = "SELECT * FROM tasks";
$result = [];

// データベースに接続
$mysqli = new mysqli($host, $user, $password, $database);

// タスク一覧を取得
if ($mysqli->connect_error) {
  die("接続失敗: " . $mysqli->connect_error);
} else {
  $query = $mysqli->query($sql);

  // 取得した一覧から1行ずつ連想配列で取得
  while ($row = $query->fetch_assoc()) {
    // 一覧表示に必要な情報だけを取得し格納
    $taskData = array(
      "title" => $row["title"],
      "description" => $row["description"],
      "status" => $row["status"],
      "due_date" => $row["due_date"],
    );

    $result[] = $taskData;
  }
}

// タスクがフォームから送信された場合、データベースに挿入
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // フォームから送信されたデータを取得 (input要素のname属性に対応)
  $newTaskTitle = $_POST["new_title"];
  $newTaskDescription = $_POST["new_task_description"];
  $newTaskStatus = $_POST["new_task_status"];
  $newTaskDueDate = $_POST["new_task_due_date"];

  // データベースに新しいタスクを追加するSQL
  $insertTaskSql = "INSERT INTO tasks (title, description, status, due_date) VALUES (?, ?, ?, ?)";

  // プリペアードステートメントの作成(SQL文の準備)
  $stmt = $mysqli->prepare($insertTaskSql);

  // エラーチェック(SQL文の)
  if (!$stmt) {
    // セッションに保存
    $_SESSION['error_message'] = "エラー：作成に失敗しました";
  } else {
    // パラメータをバインド
    $stmt->bind_param("ssss", $newTaskTitle, $newTaskDescription, $newTaskStatus, $newTaskDueDate);

    // ステートメントを実行
    $executeResult = $stmt->execute();

    // ステートメントを閉じる
    $stmt->close();

    // エラーチェック(タスク作成)
    if ($executeResult) {
      $_SESSION['error_message'] = "新しいタスクを作成しました";
    } else {
      $_SESSION['error_message'] = "エラー：タスクの作成に失敗しました";
    }
  }
    // リダイレクト
    header("Location: " . $_SERVER["PHP_SELF"]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>task</title>
    <link rel="stylesheet" type="text/css" href="../../css/styles.css">
  </head>
  <body>
    <h1>タスク作成</h1>
    <?php
    // セッションに成功メッセージが保存されていれば表示
    if (isset($_SESSION['success_message'])) {
        echo "<div style='color: green;'>{$_SESSION['success_message']}</div>";
        // メッセージを表示したらセッションから削除
        unset($_SESSION['success_message']);
    }

    // セッションにエラーメッセージが保存されていれば表示
    if (isset($_SESSION['error_message'])) {
        echo "<div style='color: red;'>{$_SESSION['error_message']}</div>";
        // メッセージを表示したらセッションから削除
        unset($_SESSION['error_message']);
    }
    ?>
    <!-- タスク作成フォーム -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
      <label for="new_title">タイトル:</label>
      <input type="text" id="new_title" name="new_title" required><br>

      <label for="new_task_description">説明:</label>
      <textarea id="new_task_description" name="new_task_description" rows="4" cols="50" required></textarea><br>

      <select id="new_task_status" name="new_task_status" required>
          <option value="未達成">未達成</option>
          <option value="達成">達成</option>
      </select><br>
      
      <label for="new_task_due_date">期限:</label>
      <input type="date" id="new_task_due_date" name="new_task_due_date" required><br>

      <button type="submit">タスクを作成</button>
    </form>
  </body>
</html>