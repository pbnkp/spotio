exports.actions = (app, argv, options) ->
  
  app.get '/', (req, res) ->
    res.render 'index'
