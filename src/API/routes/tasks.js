const express = require('express');
const router = express.Router();

// Routes pour les tâches
router.get('/', (req, res) => {
  res.send('Liste des tâches');
});

module.exports = router;
