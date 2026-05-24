import { motion } from "framer-motion";

export default function Table({ headers, rows, renderRow }) {
  return (
    <div className="overflow-hidden rounded-2xl border border-white/10 bg-white/[0.02]">
      <div className="overflow-x-auto">
        <table className="min-w-full text-sm">
          <thead className="border-b border-white/10 bg-white/5">
            <tr>
              {headers.map((header) => (
                <th
                  key={header}
                  className="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-white/60"
                >
                  {header}
                </th>
              ))}
            </tr>
          </thead>
          <motion.tbody
            initial="hidden"
            animate="visible"
            variants={{
              hidden: {},
              visible: { transition: { staggerChildren: 0.05 } },
            }}
            className="divide-y divide-white/5"
          >
            {rows.length === 0 ? (
              <tr>
                <td
                  colSpan={headers.length}
                  className="px-6 py-8 text-center text-sm text-white/50"
                >
                  Tidak ada data.
                </td>
              </tr>
            ) : (
              rows.map(renderRow)
            )}
          </motion.tbody>
        </table>
      </div>
    </div>
  );
}
