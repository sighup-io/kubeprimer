var mongo = require('mongodb');
var monk = require('monk');
var mongodbUrl = process.env.MONGO_HOST; // Setting via Env Variable
var db = monk(mongodbUrl + ':27017/kubeprimer');

// Make our db accessible to our router
var appRouter = function(app) {
    var collection = db.get('list')

    // curl -X POST --data 'task=test01' http://kubeprimer-backe/add
    app.post("/add", function(req, res) {
        task = req.body.task;

        // Submit to the DB
        collection.insert({
            "task" : task,
        }, function (err, doc) {
            if (err) {
                // If it failed, return error
                res.status(400).send("There was a problem adding the information to the database.");
            }
        });
        
        res.status(200).end("ok");
    });

    app.delete("/delete", function(req, res) {
        id = req.body.id;

        // Submit to the DB
        collection.remove({
            "_id" : id,
        }, function (err, doc) {
            if (err) {
                // If it failed, return error
                res.status(400).send("There was a problem removing the information to the database.");
            }
        });
        
        res.status(200).end("ok");
    });

    app.get("/tasks", function(req, res) {
        // Submit to the DB
        collection.find({},{},function(e,docs){
            res.json({"tasks": docs});
        });
    });
}

module.exports = appRouter;
