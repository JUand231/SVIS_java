package co.edu.sena.cimm.svis.servlet;

import co.edu.sena.cimm.svis.dto.ApiError;
import co.edu.sena.cimm.svis.dto.VotoRequest;
import co.edu.sena.cimm.svis.dto.VotoResponse;
import co.edu.sena.cimm.svis.service.VotoService;
import co.edu.sena.cimm.svis.util.JsonUtil;
import javax.servlet.annotation.WebServlet;
import javax.servlet.http.HttpServlet;
import javax.servlet.http.HttpServletRequest;
import javax.servlet.http.HttpServletResponse;
import java.io.BufferedReader;
import java.io.IOException;

@WebServlet("/api/votar")
public class VotoServlet extends HttpServlet {

    private VotoService votos;

    @Override
    public void init() {
        votos = new VotoService();
    }

    @Override
    protected void doPost(HttpServletRequest req, HttpServletResponse resp) throws IOException {
        resp.setContentType("application/json;charset=UTF-8");
        req.setCharacterEncoding("UTF-8");

        try {
            BufferedReader r = req.getReader();
            StringBuilder sb = new StringBuilder();
            String linea;
            while ((linea = r.readLine()) != null) {
                sb.append(linea);
            }
            VotoRequest in = JsonUtil.fromJson(sb.toString(), VotoRequest.class);

            if (in == null || in.codigo == null || in.opcionId == null
                    || in.encuestaId == null || in.usuarioId == null) {
                resp.setStatus(HttpServletResponse.SC_BAD_REQUEST); // 400
                resp.getWriter().write(JsonUtil.toJson(
                        new ApiError("DATOS", "codigo, opcionId, encuestaId y usuarioId requeridos")));
                return;
            }

            // REGLA 2 y 3: quema atómica + FOR UPDATE dentro del service
            VotoResponse out = votos.emitirVoto(in.codigo, in.opcionId, in.encuestaId, in.usuarioId);

            if (out.exito) {
                resp.setStatus(HttpServletResponse.SC_OK); // 200
                resp.getWriter().write(JsonUtil.toJson(out));
                return;
            }
            // REGLA 3: doble voto -> 409 Conflict para que el front lo distinga
            String m = String.valueOf(out.mensaje);
            if (m.contains("Ya votaste")) {
                resp.setStatus(HttpServletResponse.SC_CONFLICT); // 409
            } else if (m.contains("no existe")) {
                resp.setStatus(HttpServletResponse.SC_NOT_FOUND); // 404
            } else {
                resp.setStatus(HttpServletResponse.SC_BAD_REQUEST); // 400 expirado/cerrada/opcion
            }
            resp.getWriter().write(JsonUtil.toJson(out));

        } catch (RuntimeException e) {
            resp.setStatus(HttpServletResponse.SC_BAD_REQUEST); // 400 datos incompletos
            resp.getWriter().write(JsonUtil.toJson(new ApiError("DATOS", e.getMessage())));
        } catch (Exception e) {
            resp.setStatus(HttpServletResponse.SC_INTERNAL_SERVER_ERROR); // 500
            resp.getWriter().write(JsonUtil.toJson(new ApiError("ERROR", e.getMessage())));
        }
    }
}
