<div class="modal fade" id="reservationModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Réservation</h4>
            </div>

            <div class="modal-body">
                <h4> Souhaitez-vous vraiment reserver cette séance ? </h4>

                <form class="form-horizontal reservationForm">
                    <div class="form-group  div-choix-coach">
                        <label for="choix-coach" class="col-md-4 control-label"> Souhaitez-vous un coach lors de la séance ?</label>

                        <div class="col-md-6">
                            <input id="choix-coach" type="checkbox" class="form-control" name="choix-coach" checked>
                        </div>
                    </div>

                    <div class="form-group div-select-coach">
                        <label for="select-coach" class="col-md-4 control-label"> Veuillez choisir votre coach</label>

                        <div class="col-md-6">
                            <select id="select-coach" class="form-control" name="select-coach">

                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="reservation-seance">
                   Réserver
                </button>

                
            </div>
        </div>
    </div>
</div>