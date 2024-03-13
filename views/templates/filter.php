<form id="filters" class="filters" action="/collaboration" method="POST">
        <div class="filters-inputs">
            <h3>Filtros:</h3>
            <div class="campo">
                <label for="all">Todas</label>
                <input type="radio" id="all" name="filter" value="" checked/>
            </div>

            <div class="campo">
                <label for="toDo">Pendientes</label>
                <input type="radio" id="toDo" name="filter" value="0"/>
            </div>

            <div class="campo">
                <label for="inProgress">En progreso</label>
                <input type="radio" id="inProgress" name="filter" value="1"/>
            </div>
            <div class="campo">
                <label for="finish">Finalizadas</label>
                <input type="radio" id="finish" name="filter" value="2"/>
            </div>
        </div>
    </form>