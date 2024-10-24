const express = require('express');
const cors = require('cors');
const mysql = require('mysql');
const app = express();
const port = 3000;

app.use(express.json());
app.use(cors());

// Récuperer la listes des taches
app.get('/tasks', (req, res) => {
  res.send([
    { task1: 'Démarrer projet' },
  ]);
});

app.listen(port, () => {
  console.log(`Server started on http://localhost:${port}`);
});