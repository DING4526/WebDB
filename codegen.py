#!/usr/bin/env python3
import argparse
import glob
from pathlib import Path
import sys

# 用法：
# python codegen.py

# =====================================================
# 你只要编辑这里即可，每个 preset 只需要两个字段：
#   "files": 要合并的文件列表（可包含 glob）
#   "output": 输出文件名
# =====================================================
PRESETS = {
    "backend": {
        "files": [
            "backend/controllers/*.php",
            "backend/models/*.php",
            # "backend/views/layouts/*.php",
            # "backend/views/site/*.php",
            # "backend/views/taskboard/*.php",
            "backend/views/*/*.php",
        ],
        "output": "_current_code/backend.txt"
    },
    "common": {
        "files": [
            "common/models/*.php",
        ],
        "output": "_current_code/common.txt"
    },
    "frontend": {
        "files": [
            "frontend/controllers/*.php",
            "frontend/models/*.php",
            "frontend/views/layouts/*.php",
            "frontend/views/site/*.php",
        ],
        "output": "_current_code/frontend.txt"
    },
    "console": {
        "files": [
            "console/controllers/*.php",
            "console/models/*.php",
            "console/migrations/*.php",
        ],
        "output": "_current_code/console.txt"
    },
    "models": {
        "files": [
            "common/models/*.php",
        ],
        "output": "_current_code/models.txt"
    },
    "team": {
        "files": [
            "common/models/Team.php",
            "common/models/TeamMember.php",
            "backend/views/team/index.php",
            "backend/views/team-member/index.php",
            "backend/controllers/TeamController.php",
            "backend/controllers/TeamMemberController.php",
            "backend/models/TeamSearch.php",
            "backend/models/TeamMemberSearch.php",
        ],
        "output": "_current_code/team_module.txt"
    },
    "war-event": {
        "files": [
            "backend/views/war-event/*.php",
            "backend/controllers/WarEventController.php",
            "common/models/WarEvent.php",
            "backend/web/css/war-event.css",
        ],
        "output": "_current_code/war-event.txt"
    },
    "war-message": {
        "files": [
            "backend/views/war-message/*.php",
            "backend/controllers/WarMessageController.php",
            "common/models/WarMessage.php",
            # "backend/web/css/war-message.css",
        ],
        "output": "_current_code/war-message.txt"
    },
    "war-person": {
        "files": [
            "backend/views/war-person/*.php",
            "backend/controllers/WarPersonController.php",
            "common/models/WarPerson.php",
        ],
        "output": "_current_code/war-person.txt"
    },
}
# =====================================================


def read_files_from_listfile(listfile):
    out = []
    p = Path(listfile)
    with p.open("r", encoding="utf-8") as f:
        for ln in f:
            ln = ln.strip()
            if ln and not ln.startswith("#"):
                out.append(ln)
    return out


def expand_globs_and_normalize(files):
    result = []
    missing = []

    for item in files:
        # 展开 glob
        if any(ch in item for ch in "*?[]{}"):
            matches = glob.glob(item, recursive=True)
            if matches:
                result.extend(matches)
                continue

        # 非 glob，直接处理
        p = Path(item)
        if not p.is_absolute():
            p = (Path.cwd() / p).resolve()

        if p.exists() and p.is_file():
            result.append(str(p))
        else:
            missing.append(str(p))

    # 去重保持顺序
    seen = set()
    unique = []
    for f in result:
        if f not in seen:
            unique.append(f)
            seen.add(f)

    return unique, missing


def main():
    parser = argparse.ArgumentParser(
        description="简单直选文件合并工具（支持 preset）"
    )

    parser.add_argument("--preset", choices=PRESETS.keys(),
                        help="使用预设（预设中定义 files 与 output）")

    parser.add_argument("--files", nargs="*",
                        help="直接指定要合并的文件（命令行优先于 preset）")

    parser.add_argument("--files-from",
                        help="从列表文件中读取要合并的文件（命令行优先）")

    parser.add_argument("-o", "--output",
                        help="输出文件名（覆盖 preset）")

    parser.add_argument("--all-presets", action="store_true", default=True,
                        help="一次性生成所有 preset 的输出")

    args = parser.parse_args()

    # ==== 一次性生成所有 preset ====
    if args.all_presets:
        for name, preset in PRESETS.items():
            files, missing = expand_globs_and_normalize(preset["files"])

            if missing:
                print(f"  - 有缺失文件（已跳过）：")
                for m in missing:
                    print("    ", m)

            output = preset["output"]
            Path(output).parent.mkdir(parents=True, exist_ok=True)

            total_lines = 0  # <<< 新增：统计行数

            with open(output, "w", encoding="utf-8") as out:
                header = f"**[{name}]**"
                out.write(header)
                total_lines += header.count("\n") + 1   # 记录头部行数

                for f in files:
                    section_header = f"\n\n==== {f} ====\n\n"
                    out.write(section_header)
                    total_lines += section_header.count("\n")

                    try:
                        with open(f, "r", encoding="utf-8") as src:
                            content = src.read()
                            out.write(content)
                            total_lines += content.count("\n") + 1
                    except Exception as e:
                        err_msg = f"[无法读取文件: {e}]\n"
                        out.write(err_msg)
                        total_lines += 1

            print(f"[INFO] 生成 {name} : {total_lines} 行")

        return  # 全部结束，不再进入单 preset 模式

    final_files = []
    final_output = None

    # 1. 优先读取命令行指定的文件来源
    if args.files_from:
        final_files.extend(read_files_from_listfile(args.files_from))
    if args.files:
        final_files.extend(args.files)

    # 2. 若命令行未指定文件，则使用 preset
    if not final_files:
        if args.preset:
            preset = PRESETS[args.preset]
            final_files.extend(preset["files"])
            final_output = preset["output"]
        else:
            print("错误：必须使用 --preset 或 --files/--files-from 指定输入文件。", file=sys.stderr)
            sys.exit(1)

    # 3. 解析输出路径
    if args.output:
        final_output = args.output
    elif not final_output:
        final_output = "merged_output.txt"

    # 4. 处理 globs 和路径
    valid_files, missing = expand_globs_and_normalize(final_files)

    if missing:
        print("以下文件不存在或无法访问（已跳过）：", file=sys.stderr)
        for m in missing:
            print("  -", m, file=sys.stderr)

    if not valid_files:
        print("没有可合并的有效文件。", file=sys.stderr)
        sys.exit(1)

    # 5. 合并写入
    with open(final_output, "w", encoding="utf-8") as out:
        for f in valid_files:
            out.write(f"\n==== {f} ====\n\n")
            try:
                with open(f, "r", encoding="utf-8") as src:
                    out.write(src.read())
            except Exception as e:
                out.write(f"[无法读取文件: {e}]\n")

    print(f"已合并 {len(valid_files)} 个文件到: {final_output}")


if __name__ == "__main__":
    main()
