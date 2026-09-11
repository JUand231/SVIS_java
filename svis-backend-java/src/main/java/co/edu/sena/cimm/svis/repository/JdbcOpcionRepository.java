package co.edu.sena.cimm.svis.repository;

import co.edu.sena.cimm.svis.model.Opcion;
import java.util.List;

public class JdbcOpcionRepository implements OpcionRepository{

    @Override
    public List<Opcion> listarOpcionesPorEncuesta(Long idEncuesta) {
        throw new UnsupportedOperationException("Not supported yet."); 
    }

    @Override
    public void crearOpcion(Long idEncuesta, String texto) {
        throw new UnsupportedOperationException("Not supported yet."); 
    }

    @Override
    public int obtenerContadorVotos(Long idOpcion) {
        throw new UnsupportedOperationException("Not supported yet."); 
    }

    @Override
    public void sumarVoto(Long idOpcion) {
        throw new UnsupportedOperationException("Not supported yet."); 
    }
    
}
