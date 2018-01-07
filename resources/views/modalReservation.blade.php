<div class="modal fade" id="reservationModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Réservation</h4>
            </div>

            <div class="modal-body">
                @if($utilisateurValide == false)
                    <div class="alert alert-danger">
                        Votre abonnement ou votre carte n'est pas valide, vous devrez payer cette séance à l'unité.
                        <a href="{{ url('/compte') }}" class="button">Prendre un abonnement</a>
                    </div>
                @endif

                <div class="alert alert-warning message-recommandations">
                    Cette séance n'a pas plus de places disponibles. C'est pourquoi nous vous proposons des recommandations.
                </div>

                <form class="form-horizontal reservationForm">

                    <div class="form-group div-ajout-personne">
                        <label for="ajout-personne" class="col-md-4 control-label">Ajouter des personnes</label>

                        <div class="col-md-6">
                            <ul class="input-group">
                                <select id="ajout-personne" class="form-control" name="ajout-personne">
                                    <option id='default-value-ajout-personnes' value="default">Selectionner les personnes à ajouter</option>
                                </select>

                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-primary bouton-ajout-personne">Ajouter</button>
                                </span>
                            </ul>

                            <ul class="list-group listePersonneAAjouter">

                            </ul>

                            <div class="alert alert-warning warning-places-restantes">
                                Il ne reste qu'une place disponible pour cette séance, vous ne pouvez donc pas ajouter de personnes.
                            </div>
                        </div>
                    </div>

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
                <button type="button" class="btn btn-primary" id="reservation-seance" data-url="{{ url('client/effectuerReservation') }}">
                    @if($utilisateurValide)
                        Réserver
                    @else
                        Payer à l'unité
                    @endif
                </button>

                <a id='lien-recommandations' href="" title="Les recommandations sont des séances suseptibles de vous interesser">Voir nos recommandations</a>
            </div>
        </div>
    </div>
</div>