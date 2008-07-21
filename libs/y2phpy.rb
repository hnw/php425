#!/usr/bin/env ruby
grammar = []
is_cfg = false
is_grammar = false
non_terminal = nil

def puts_grammar non_terminal, grammar
  prefix = grammar[0] == '|' ? grammar.shift : nil

  grammar_without_comment = grammar.join(' ').gsub(%r|/\*.*\*/|, '').split;

  arguments = Array.new(grammar_without_comment.length) {|i| "$#{i+1}"}
  arguments = 'array('+arguments.join(', ')+')'
  arguments = ["'#{non_terminal}'", arguments].join(', ')

  hook_script = "{ %s = %s(%s); }" % ['$$', 'phpParser::execute', arguments]

  puts "\t%s\t%s\t%s" % [prefix, grammar.join(' '), hook_script]
end

while line = ARGF.gets
  [[/\{[^'].+?[^']\}/, ''], [/(\s)T_/, '\1TT_'], [/^#.*$/, '']].each {|arg|
    line.gsub! arg[0], arg[1]
  }
  line.rstrip!

  is_cfg = !is_cfg if line[0, 2] == '%%'

  if is_grammar
    new_grammar = line.split
    case new_grammar[0]
    when '|'
      puts_grammar non_terminal, grammar
      grammar = new_grammar
    when ';'
      puts_grammar non_terminal, grammar
      puts ';'
      grammar = []
    else
      grammar += new_grammar
    end
  else
    puts line
  end

  if is_cfg && (/^(\w+):/ =~ line || line == ';')
    is_grammar = !is_grammar
    non_terminal = $1
  end
end
