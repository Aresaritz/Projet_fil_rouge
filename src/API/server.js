const express = require("express");
const cors = require("cors");
const mysql = require("mysql2");
const path = require("path");
const bodyParser = require("body-parser");
const app = express();
const port = 3000;

// Middleware
app.use(express.json());
app.use(bodyParser.json());
app.use(cors());

// Connexion à la base de données MySQL
const db = mysql.createConnection({
  host: "localhost",
  user: "root", // Ton utilisateur MySQL
  password: "", // Ton mot de passe MySQL
  database: "fil_rouge", // Le nom de ta base de données
});

// Connexion à la base de données
db.connect((err) => {
  if (err) {
    console.error("Erreur de connexion à la base de données:", err);
    return;
  }
  console.log("Connecté à la base de données MySQL");
});

// Servir les fichiers statiques
app.use(express.static(path.join(__dirname, "../../public")));

// Route pour renvoyer la page index.html
app.get("/", (req, res) => {
  res.sendFile(path.join(__dirname, "../../public/index.html"));
});

// Route pour récupérer le nom d'utilisateur par ID
app.get("/user/:id", (req, res) => {
  const userId = req.params.id;

  const query = "SELECT username FROM users WHERE id = ?";
  db.query(query, [userId], (error, results) => {
    if (error) {
      return res
        .status(500)
        .json({ success: false, message: "Database error", error });
    }

    if (results.length === 0) {
      return res
        .status(404)
        .json({ success: false, message: "User not found" });
    }

    const username = results[0].username; // Récupérer le nom d'utilisateur
    res.json({ success: true, username }); // Retourner le nom d'utilisateur
  });
});

// Fonction pour vérifier si l'utilisateur existe dans la base de données
function userExists(username, callback) {
  const query = "SELECT * FROM users WHERE username = ?";
  db.query(query, [username], (err, results) => {
    if (err) {
      return callback(err);
    }
    if (results.length > 0) {
      return callback(null, true); // L'utilisateur existe
    }
    return callback(null, false); // L'utilisateur n'existe pas
  });
}

// Fonction pour valider les identifiants (validation côté client)
function userCredentialsValid(username, password) {
  return (
    username.length >= 4 &&
    username.length <= 20 &&
    password.length >= 8 &&
    password.length <= 40 &&
    /[a-z]/.test(password) &&
    /[A-Z]/.test(password) &&
    /\d/.test(password) &&
    /[!@#$%^&*(),.?":{}|<>]/.test(password)
  );
}

// Fonction pour ajouter un utilisateur dans la base de données
function addUser(username, password, callback) {
  const query = "INSERT INTO users (username, password) VALUES (?, ?)";
  db.query(query, [username, password], (err, results) => {
    if (err) {
      return callback(err);
    }
    return callback(null, results);
  });
}

// Route pour gérer l'inscription via fetch
app.post("/signup", (req, res) => {
  const { username, password } = req.body;

  // Validation des champs
  if (!username || !password) {
    return res.json({ success: false, message: "Please fill in all fields" });
  }

  // Vérification si les identifiants sont valides
  if (!userCredentialsValid(username, password)) {
    return res.json({ success: false, message: "Invalid credentials" });
  }

  // Vérifier si l'utilisateur existe déjà
  userExists(username, (err, exists) => {
    if (err) {
      return res
        .status(500)
        .json({ success: false, message: "Database error" });
    }

    if (exists) {
      return res.json({ success: false, message: "User already exists" });
    }

    // Ajouter l'utilisateur dans la base de données
    addUser(username, password, (err) => {
      if (err) {
        return res
          .status(500)
          .json({ success: false, message: "Failed to register user" });
      }

      return res.json({
        success: true,
        message: "User registered successfully",
      });
    });
  });
});

// Fonction pour vérifier les identifiants de connexion
function verifyUser(username, password, callback) {
  const query = "SELECT id, password FROM users WHERE username = ?";
  db.query(query, [username], (err, results) => {
    if (err) {
      return callback(err);
    }
    if (results.length === 0) {
      return callback(null, false); // L'utilisateur n'existe pas
    }

    const user = results[0];
    // Vérifier si le mot de passe correspond
    if (user.password === password) {
      return callback(null, true, user.id); // Identifiants corrects
    } else {
      return callback(null, false); // Mot de passe incorrect
    }
  });
}

// Route pour gérer la connexion via fetch
app.post("/login", (req, res) => {
  const { username, password } = req.body;

  // Validation des champs
  if (!username || !password) {
    return res.json({ success: false, message: "Please fill in all fields" });
  }

  // Vérification des identifiants dans la base de données
  verifyUser(username, password, (err, valid, userId) => {
    if (err) {
      return res
        .status(500)
        .json({ success: false, message: "Database error" });
    }

    if (!valid) {
      return res.json({
        success: false,
        message: "Invalid username or password",
      });
    }

    // Si les identifiants sont valides
    return res.json({
      success: true,
      message: "Login successful!",
      id: userId,
    });
  });
});

// Route pour récupérer tous les utilisateurs
app.get("/users", (req, res) => {
  const query = "SELECT id, username FROM users"; // Assurez-vous de sélectionner l'ID également
  db.query(query, (err, results) => {
    if (err) {
      return res
        .status(500)
        .json({ success: false, message: "Database error" });
    }
    res.json({ success: true, users: results }); // Envelopper les résultats dans un objet
  });
});

app.get("/name", (req, res) => {
  const query = "SELECT name FROM users WHERE id = ?";
  db.query(query, [req.query.id], (err, results) => {
    if (err) {
      return res.status(500).json({ error: "Database error" });
    }
    res.json(results);
  });
});

app.post("/tasks", (req, res) => {
  const { task, priority, to, date } = req.body;

  // Valider les données
  if (!task || !priority || !to || !date) {
    return res
      .status(400)
      .json({ success: false, message: "All fields are required." });
  }

  // Requête d'insertion dans la base de données
  const query =
    "INSERT INTO taches (tache, priorité, assignation, fin) VALUES (?, ?, ?, ?)";
  db.query(query, [task, priority, to, date], (err, results) => {
    if (err) {
      return res
        .status(500)
        .json({ success: false, message: "Database error." });
    }
    res.status(201).json({
      success: true,
      message: "Task added successfully.",
      taskId: results.insertId,
    });
  });
});

// Démarrer le serveur
app.listen(port, () => {
  console.log(`Server started on http://localhost:${port}`);
});
