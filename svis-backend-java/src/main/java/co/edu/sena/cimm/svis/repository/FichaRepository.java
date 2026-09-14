package co.edu.sena.cimm.svis.repository;

import co.edu.sena.cimm.svis.model.Ficha;
import java.util.List;

public interface FichaRepository {

    List<Ficha> listarTodas();

    Ficha obtenerPorId(Long id);

    Ficha obtenerPorNumero(String numero);

    void crear(String numero, String programa, String jornada);
}
