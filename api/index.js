const sqllite3 = require("sqllite3").verbose();

// create a db

const db = new sqllite3.Database("./games.sqllite", function (err) {
    if (err) {
        console.log("Error connecting to database");

    } else
        console.log("Database Connected: Games");
});

module.exports = db;


const db = require("../database")